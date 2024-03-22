<?php

namespace FontLib\Table\Type;

use FontLib\Table\Table;

/**
 * `cmap` font table.
 */
class cmap extends Table {
  private const HEADER_FORMAT = [
    "version" => self::UINT16,
    "numberSubtables" => self::UINT16,
  ];

  private const SUBTABLE_HEADER_FORMAT = [
    "platformID" => self::UINT16,
    "platformSpecificID" => self::UINT16,
    "offset" => self::UINT32,
  ];

  private const SUBTABLE_V4_FORMAT = [
    "length" => self::UINT16,
    "language" => self::UINT16,
    "segCountX2" => self::UINT16,
    "searchRange" => self::UINT16,
    "entrySelector" => self::UINT16,
    "rangeShift" => self::UINT16,
  ];

  private const SUBTABLE_V12_FORMAT = [
    "length" => self::UINT32,
    "language" => self::UINT32,
    "ngroups" => self::UINT32,
  ];

  /**
   * @var array
   */
  private $data;

  /**
   * {@inheritdoc}
   */
  protected function _parse(): void {
    $font = $this->getFont();

    $cmap_offset = $font->pos();

    $data = $font->unpack(self::HEADER_FORMAT);

    $subtables = [];
    for ($i = 0; $i < $data["numberSubtables"]; $i++) {
      $subtables[] = $font->unpack(self::SUBTABLE_HEADER_FORMAT);
    }

    $data["subtables"] = $subtables;

    foreach ($data["subtables"] as &$subtable) {
      $font->seek($cmap_offset + $subtable["offset"]);

      $subtable["format"] = $font->readUInt16();

      if (($subtable["format"] !== 4) && ($subtable["format"] !== 12)) {
        unset($data["subtables"][array_search($subtable, $data["subtables"])]);
        $data["numberSubtables"]--;
        continue;
      }

      if ($subtable["format"] === 12) {
        $font->readUInt16();

        $subtable += $font->unpack(self::SUBTABLE_V12_FORMAT);

        $glyphIndexArray = [];
        $endCodes = [];
        $startCodes = [];

        for ($p = 0; $p < $subtable['ngroups']; $p++) {
          $startCode = $startCodes[] = $font->readUInt32();
          $endCode = $endCodes[] = $font->readUInt32();
          $startGlyphCode = $font->readUInt32();

          for ($c = $startCode; $c <= $endCode; $c++) {
            $glyphIndexArray[$c] = $startGlyphCode;
            $startGlyphCode++;
          }
        }

        $subtable += [
          "startCode" => $startCodes,
          "endCode" => $endCodes,
          "glyphIndexArray" => $glyphIndexArray,
        ];

      } else if ($subtable["format"] === 4) {

        $subtable += $font->unpack(self::SUBTABLE_V4_FORMAT);

        $segCount = $subtable["segCountX2"] / 2;
        $subtable["segCount"] = $segCount;

        try {
          $endCode = $font->readUInt16Many($segCount);
        } catch (\Exception $e) {
          // Handle error
          return;
        }

        $font->readUInt16(); // reservedPad

        try {
          $startCode = $font->readUInt16Many($segCount);
          $idDelta = $font->readInt16Many($segCount);
        } catch (\Exception $e) {
          // Handle error
          return;
        }

        $ro_start = $font->pos();
        try {
          $idRangeOffset = $font->readUInt16Many($segCount);
        } catch (\Exception $e) {
          // Handle error
          return;
        }

        $glyphIndexArray = [];
        for ($i = 0; $i < $segCount; $i++) {
          $c1 = $startCode[$i];
          $c2 = $endCode[$i];
          $d = $idDelta[$i];
          $ro = $idRangeOffset[$i];

          if ($ro > 0) {
            $font->seek($subtable["offset"] + 2 * $i + $ro);
          }

          for ($c = $c1; $c <= $c2; $c++) {
            if ($ro == 0) {
              $gid = ($c + $d) & 0xFFFF;
            } else {
              $offset = ($c - $c1) * 2 + $ro;
              $offset = $ro_start + 2 * $i + $offset;

              $font->seek($offset);
              $gid = $font->readUInt16();

              if ($gid != 0) {
                $gid = ($gid + $d) & 0xFFFF;
              }
            }

            if ($gid > 0) {
              $glyph
