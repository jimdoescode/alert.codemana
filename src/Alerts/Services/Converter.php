<?php namespace Alerts\Services;

use \Alerts\Models;

class Converter
{
    /**
     * @param string $fileName
     * @param string $rawFile
     * @param array $editors
     * @return Models\PatchFile
     */
    public function patchToModel($fileName, $rawFile, $editors)
    {
        $patch = new Models\PatchFile();
        $patch->chunks = [];
        $patch->raw = $rawFile;
        $patch->name = $fileName;
        $patch->editors = $editors;

        if (preg_match_all('/@@ \-(\d+),\d+ \+(\d+),\d+ @@/', $rawFile, $matches, PREG_OFFSET_CAPTURE)) {

            $count = count($matches[0]);
            for ($i = 0; $i < $count; $i++) {
                $chunk = new Models\PatchChunk();
                $chunk->header = $matches[0][$i][0];
                $rawChunk = $i < ($count - 1) ?
                    substr($rawFile, $matches[0][$i][1], $matches[0][$i+1][1] - $matches[0][$i][1]) :
                    substr($rawFile, $matches[0][$i][1]);

                $negLineNumber = $matches[1][$i][0];
                $posLineNumber = $matches[2][$i][0];
                $lines = explode("\n", $rawChunk);
                array_shift($lines);
                foreach ($lines as $line) {
                    //Diff outputs this extra line if there is no newline at
                    //the end of a file so we need to detect it and remove it
                    if ($line === 'No newline at end of file') {
                        break;
                    }

                    $parsedLine = new Models\PatchLine();
                    $parsedLine->raw = $line;
                    $parsedLine->isAdded = ($line[0] === '+');
                    $parsedLine->isRemoved = ($line[0] === '-');
                    $parsedLine->parsed = htmlspecialchars(substr($line, 1));
                    if ($parsedLine->isAdded) {
                        $parsedLine->newNumber = $posLineNumber;
                        $posLineNumber++;
                    } elseif ($parsedLine->isRemoved) {
                        $parsedLine->oldNumber = $negLineNumber;
                        $negLineNumber++;
                    } else {
                        $parsedLine->newNumber = $posLineNumber;
                        $parsedLine->oldNumber = $negLineNumber;
                        $posLineNumber++;
                        $negLineNumber++;
                    }
                    $chunk->lines[] = $parsedLine;
                }
                $patch->chunks[] = $chunk;
            }
        }

        return $patch;
    }
}
