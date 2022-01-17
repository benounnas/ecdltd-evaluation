<?php


/**
 cli example : 
 ` php parser.php --file products_comma_separated.csv --unique-combinations combination_count.csv `
  
 options required : 
    --file: the input file to be read
    --unique-combinations : the combination file to base the output on

    IMPORTANT: 
    to make the field required, please add '*' before the commas in csv combination

 */

class Product
{
    public function __construct($combinations = [], $values = [], $line = null)
    {

        for ($i = 0; $i < count($combinations); $i++) {
            $required = strpos($combinations[$i], '*');

            if (empty($values[$i]) && $required) {
                throw new Exception("Field required in line " . ($line + 1) . "\n");
            } else if (!empty($values[$i])) {
                $unstarRequiredCombination = str_replace('*', '', $combinations[$i]);
                $this->$unstarRequiredCombination = $values[$i];
            } else {
                $this->{$combinations[$i]} = NULL;
            }
        }
    }
}

$inputFormatsArray = ['csv', 'tsv']; // input file supported formats
$combinationFormatArray = ['csv']; // combination file supported formats


/**
 * Getting the CLI arguments
 */
$longopts  = array(
    // Required values
    "file:",
    "unique-combinations:"
);

$options = getopt('', $longopts);






/**
 * Checking the input file
 */

echo "-Input File : " . $options["file"] . "\n";

if (empty($options["file"])) {
    throw new Exception("File argument is missing");
}
if (!is_file($options["file"])) {
    throw new Exception("Invalid input file");
}


/**
 * Checking the combination file
 */
echo "-Combination File : " . $options["unique-combinations"] . "\n";
if (empty($options["unique-combinations"])) {

    throw new Exception("Combination file argument is missing");
}


if (!is_file($options["unique-combinations"])) {
    echo "Invalid Combination File  exception \n";
    throw new Exception("Invalid combination file");
}






/**
 * Checking the input file extension
 */

$InputFileExtension = pathinfo($options["file"], PATHINFO_EXTENSION);
echo "-Input File extension : " . $InputFileExtension . "\n";

if (!in_array($InputFileExtension, $inputFormatsArray)) { // depends on the supported format above
    throw new Exception("Invalid extension input file");
}


/**
 * Checking the combination file extension
 */
$CombinationFileExtension = pathinfo($options["unique-combinations"], PATHINFO_EXTENSION);
echo "-combination File extension : " . $CombinationFileExtension . "\n";

if (!in_array($CombinationFileExtension, $combinationFormatArray)) { // depends on the supported format above
    echo "-combination File  extension exception \n";
    throw new Exception("Invalid  combination file extension");
}


$combination = [];
switch ($CombinationFileExtension) {
    case 'csv':
        try {
            $csv  = new SplFileObject($options["unique-combinations"], 'r');
        } catch (RuntimeException $e) {
            printf("Error openning csv: %s\n", $e->getMessage());
        }

        while (!$csv->eof()) {
            $combination = $csv->fgetcsv();
            break;
        }
        break;

    default:
        # code...
        break;
}




switch ($InputFileExtension) {
    case 'csv':

        try {
            $csv  = new SplFileObject($options["file"], 'r');
        } catch (RuntimeException $e) {
            printf("Error openning csv: %s\n", $e->getMessage());
        }

        $i = 0;
        while (!$csv->eof()) {
            $row = $csv->fgetcsv();
            if ($i > 0) {
                $productNew = new Product($combination, $row, $i);
                print_r($productNew);
            }
            $i++;
        }
        break;

    default:
        # code...
        break;
}
