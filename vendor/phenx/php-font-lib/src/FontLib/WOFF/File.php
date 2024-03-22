<?php

namespace FontLib\WOFF;

use FontLib\Table\DirectoryEntry;
use FontLib\TrueType\File as TrueTypeFile;

/**
 * WOFF font file.
 *
 * @property DirectoryEntry[] $directory
 */
class File extends TrueTypeFile
{
    public function parseHeader(): void
    {
        if (!empty($this->header)) {
            return;
        }

        $this->header = new Header($this);
        $this->header->parse();
    }

    public function load(): void
    {
        parent::load();

        $this->parseTableEntries();
        $dataOffset = $this->getPos() + count($this->directory) * 20;

        $tempFile = $this->getTempFile(false);
        $file     = $this->f;

        $this->f = $tempFile;
        $offset  = $this->header->encode();

        foreach ($this->directory as $entry) {
            // Read ...
            $this->f = $file;
            $this->seek($entry->offset);
            $data = $this->read($entry->length);

            if ($entry->origLength && $entry->length < $entry->origLength) {
                $data = gzdecode($data);
            }

            // Prepare data ...
            $length        = strlen($data);
            $entry->length = $entry->origLength = $length;
            $entry->offset = $dataOffset;

            // Write ...
            $this->f = $tempFile;

            // Woff Entry
            $this->seek($offset);
            $offset += $this->write( $entry->tag, 4); // tag
            $offset += $this->writeUInt32($dataOffset); // offset
            $offset += $this->writeUInt32($length); // length
            $offset += $this->writeUInt32($length); // origLength
            $offset += $this->writeUInt32(DirectoryEntry::computeChecksum($data)); // checksum

            // Data
            $this->seek($dataOffset);
            $dataOffset += $this->write( $data, $length);
        }

        $this->f = $tempFile;
        $this->seek(0);

        // Need to re-parse this, don't know why
        $this->header    = null;
        $this->directory = array();

