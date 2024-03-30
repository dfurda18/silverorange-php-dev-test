<?php

namespace silverorange;

use \Exception;

require_once 'src/Command/ImportPost.php';

use silverorange\DevTest\Command\ImportPost;

fwrite(STDOUT, "Welcome to the Post Importer.\n");
fwrite(STDOUT, "Actions:\n- start: starts the process from the begining.\n- exit: exits the program.\n");

$finished = false;
$state = 'start';

while(!$finished)
{
    switch($state)
    {
        case 'start':
            $importer = new ImportPost();
            fwrite(STDOUT, "This importer only supports .json extensions.\n");
            fwrite(STDOUT, "Please type the folder or file path to be imported.\n");
            break;
        default:
    }

    $input = trim(fgets(STDIN));

    switch($input)
    {
        case 'exit':
            $finished = true;
            break;
        case 'start':
            $state = 'start';
            break;
        default:
            switch($state)
            {
                case 'start':
                    try
                    {
                        $messages = $importer->import($input);
                        foreach($messages as $file => $message)
                        {
                            fwrite(STDOUT, "The file: " . $file . "\n" . $message . "\n");
                        }
                    } catch (Exception $e)
                    {
                        fwrite(STDERR, $e->getMessage() . "\n");
                    }
                    break;
                default:
                    fwrite(STDERR, "Your input:" . $input . " was not received correctly, please try again.\n");
            }
    }
}

fwrite(STDOUT, "Thank you for using the Port Importer\n");

?>
