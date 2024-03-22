<?php
declare(strict_types=1);

namespace FontLib;

/**
 * Generic font file binary stream.
 *
 * @package php-font-lib
 */
class BinaryStream {
    /**
     * @var resource The file pointer
     */
    protected $f;

    const uint8 = 1;
    const int8 = 2;
    const uint16 = 3;
    const int16 = 4;
    const uint32 = 5;
    const int32 = 6;
    const shortFrac = 7;
    const Fixed = 8;
    const FWord = 9;
    const uFWord = 10;
    const F2Dot14 = 11;
    const longDateTime = 12;
    const char = 13;

    const modeRead = "rb";
    const modeWrite = "wb";
    const modeReadWrite = "rb+";

    static function backtrace(): array {
        return debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
    }

    /**
     * Open a font file in read mode
     *
     * @param string $filename The file name of the font to open
     *
     * @return bool
     */
    public function load(string $filename): bool {
        return $this->open($filename, self::modeRead);
    }

    /**
     * Open a font file in a chosen mode
     *
     * @param string $filename The file name of the font to open
     * @param string $mode The opening mode
     *
     * @throws \Exception
     * @return bool
     */
    public function open(string $filename, string $mode = self::modeRead): bool {
        if (!in_array($mode, [self::modeRead, self::modeWrite, self::modeReadWrite])) {
            throw new \Exception("Unkown file open mode");
        }

        $this->f = fopen($filename, $mode);

        return $this->f !== false;
    }

    /**
     * Close the internal file pointer
     */
    public function close(): bool {
        return fclose($this->f) !== false;
    }

    /**
     * Change the internal file pointer
     *
     * @param resource $fp
     *
     * @throws \Exception
     */
    public function setFile($fp): void {
        if (!is_resource($fp)) {
            throw new \Exception('$fp is not a valid resource');
        }

        $this->f = $fp;
    }

    /**
     * Create a temporary file in write mode
     *
     * @param bool $allow_memory Allow in-memory files
     *
     * @return resource the temporary file pointer resource
     */
    public static function getTempFile(bool $allow_memory = true) {
        $f = null;

        if ($allow_memory) {
            $f = fopen("php://temp", "rb+");
        } else {
            $f = fopen(tempnam(sys_get_temp_dir(), "fnt"), "rb+");
        }

        return $f;
    }

    /**
     * Move the internal file pinter to $offset bytes
     *
     * @param int $offset
     *
     * @return bool True if the $offset position exists in the file
     */
    public function seek(int $offset): bool {
        return fseek($this->f, $offset, SEEK_SET) === 0;
    }

    /**
     * Gives the current position in the file
     *
     * @return int The current position
     */
    public function pos(): int {
        return ftell($this->f);
    }

    public function skip(int $n): void {
        fseek($this->f, $n, SEEK_CUR);
       
    }

    public function read(int $n): string {
        if ($n < 1) {
            return "";
        }

        return fread($this->f, $n);
    }

    public function write(string $data, ?int $length = null): int {
        if ($data === null || $data === "" || $data === false) {
            return 0;
        }

        return fwrite($this->f, $data, $length);
    }

    public function readUInt8(): int {
        return ord($this->read(1));
    }

    public function readUInt8Many(int $count): array {
        return array_map('intval', unpack("C*", $this->read($count)));
    }

    public function writeUInt8(int $data): int {
        return $this->write(chr($data), 1);
    }

    public function readInt8(): int {
        $v = $this->readUInt8();


