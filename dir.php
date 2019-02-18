<?php 

// https://stackoverflow.com/questions/34466362/how-to-get-a-list-of-files-of-subfolders-and-write-them-in-a-json-using-php
// + added filetype + filtered .DS_STORE

function dir_to_array($dir)
{
        if (! is_dir($dir)) {
                // If the user supplies a wrong path we inform him.
                return null;
        }

        // Our PHP representation of the filesystem
        // for the supplied directory and its descendant.
        $data = [];
        
        foreach (new DirectoryIterator($dir) as $f) {
                if ($f->isDot()) {
                        // Dot files like '.' and '..' must be skipped.
                        continue;
                }

                $path = $f->getPathname();
                $name = $f->getFilename();
                $filetype = $f->getExtension();

                if($name != '.DS_Store' && $name != '.gitkeep'){
	                if ($f->isFile()) {
	                        $data[] = [ 'file' => $name,
	                        'filetype' => $filetype,
                            'totalPath' => $f->getPathname(),
                             ];
	                } else{
	                        // Process the content of the directory.
	                        $files = dir_to_array($path);
                         //    $totalPath .= "/".$name ;
	                        $data[] = [ 'dir'  => $files,
	                                    'name' => $name];
	                        // A directory has a 'name' attribute
	                        // to be able to retrieve its name.
	                        // In case it is not needed, just delete it.
	                }
            	}
        }

        // Sorts files and directories if they are not on your system.
        \usort($data, function($a, $b) {
                $aa = isset($a['file']) ? $a['file'] : $a['name'];
                $bb = isset($b['file']) ? $b['file'] : $b['name'];

                return \strcmp($aa, $bb);
        });

        return $data;
}

/*
 * Converts a filesystem tree to a JSON representation.
 */
function dir_to_json($dir)
{
        $data = dir_to_array($dir);
        $data = json_encode($data);

        return $data;
}

if(isset($_GET['dir'])){
	echo dir_to_json($_GET['dir']);
}

 ?>