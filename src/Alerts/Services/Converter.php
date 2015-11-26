<?php namespace Alerts\Services;

use \Alerts\Models;

class Converter
{
    /**
     * @param string $rawFile
     * @return Models\PatchFile
     */
    public function patchToModel($rawFile)
    {
        $patch = new Models\PatchFile();
        $patch->lines = [];
        $patch->raw = $rawFile;

        if (preg_match_all('/@@\s-(\d+),.*\s@@/', $rawFile, $matches, PREG_OFFSET_CAPTURE)) {

            $count = count($matches[0]);
            for ($i = 0; $i < $count; $i++) {
                $chunk = $i < ($count - 1) ?
                    substr($rawFile, $matches[0][$i][1], $matches[0][$i+1][1]) :
                    substr($rawFile, $matches[0][$i][1]);

                $lineNumber = $matches[1][$i][0];
                $negLineNumber = $matches[1][$i][0];
                $posLineNumber = $matches[1][$i][0];
                $lines = explode("\n", $chunk);
                array_shift($lines);
                foreach ($lines as $line) {
                    $parsedLine = new Models\PatchLine();
                    $parsedLine->raw = $line;
                    $parsedLine->isAdded = ($line[0] === '+');
                    $parsedLine->isRemoved = ($line[0] === '-');
                    $parsedLine->parsed = substr($line, 1);
                    if ($parsedLine->isAdded) {
                        $parsedLine->number = $posLineNumber;
                        $posLineNumber++;
                        $lineNumber++;
                    } elseif ($parsedLine->isRemoved) {
                        $parsedLine->number = $negLineNumber;
                        $negLineNumber++;
                        $lineNumber++;
                    } else {
                        $parsedLine->number = $lineNumber;
                        $posLineNumber++;
                        $negLineNumber++;
                        $lineNumber++;
                    }
                    $patch->lines[] = $parsedLine;
                }
            }
        }

        return $patch;
    }

}
