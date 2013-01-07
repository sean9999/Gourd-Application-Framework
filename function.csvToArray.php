<?php
/**
 * Convert a comma separated file into an associated array.
 * The first row should contain the array keys.
 * 
 * Example:
 * 
 * @param string $filename Path to the CSV file
 * @param string $delimiter The separator used in the file
 * @return array
 * @link http://gist.github.com/385876
 * @author Jay Williams <http://myd3.com/>
 * @copyright Copyright (c) 2010, Jay Williams
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
function csvToArray($filename='', $delimiter=',')
{
	if(!file_exists($filename) || !is_readable($filename))
		return FALSE;

	$header = NULL;
	$data = array();
	if (($handle = fopen($filename, 'r')) !== FALSE)
	{
		while (($row = fgetcsv($handle, 0, $delimiter)) !== FALSE)
		{
			if(!$header)
				$header = $row;
			else
				$data[] = array_combine($header, $row);
		}
		fclose($handle);
	}
	return $data;
}


function simpleDump($filename='', $delimiter=',')
{
	if(!file_exists($filename) || !is_readable($filename))
		return FALSE;

	$data = array();
	if (($handle = fopen($filename, 'r')) !== FALSE)
	{
		while (($row = fgetcsv($handle, 0, $delimiter)) !== FALSE)
		{
			if (!empty($data)) {
				$data = array_merge($data,$row);
			} else {
				$data = $row;
			}
		}
		fclose($handle);
	}
	return $data;
}

?>