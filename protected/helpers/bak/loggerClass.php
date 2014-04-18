<?php

final class loggerDefaultsClass
{
	private function __construct() {}

    const LOG_LEVEL = 3; 				//0 - disable output, 1 - critical errors, 2 - functions i/o, 3 - verbose information
	const ROTATE_LOG = true;
	const FILE_MAXSIZE_LOG = 1048576;	// in bytes = 1MB
	const FILE_NAME_LOG = "creport.log";
	const FILE_PATH_LOG = __DIR__;		// or spesified path, etc.: d:\path_to_log_file
}

class loggerClass {

	static public function write($message,$level)
	{
	    if($level>loggerDefaultsClass::LOG_LEVEL || loggerDefaultsClass::LOG_LEVEL==0)
			return;
		
		// время события
		$timestamp = date('Y-m-d H:i:s',time());

		//формируем новую строку в логе
		$err_str = $timestamp.': '.$message."\n";

		// полный путь к файлу		
		$log_file =  loggerDefaultsClass::FILE_PATH_LOG . DIRECTORY_SEPARATOR . loggerDefaultsClass::FILE_NAME_LOG;

		//проверка на максимальный размер
		if (is_file($log_file) AND filesize($log_file)>=(loggerDefaultsClass::FILE_MAXSIZE_LOG))
		{
			//проверяем настройки, если установлен лог_ротэйт,
			//то "сдвигаем" старые файлы на один вниз и создаем пустой лог
			//если нет - чистим и пишем вместо старого лога
			if (loggerDefaultsClass::ROTATE_LOG === true) 
			{
				$i=1;
				//считаем старые логи в каталоге
				while (is_file($log_file.'.'.$i)) { $i++; }
				$i--;
				//у каждого из них по очереди увеличиваем номер на 1
				while ($i>0)
				{
					rename($log_file.'.'.$i,$log_file. '.' .(1+$i--));
				}
				rename ($log_file,$log_file.'.1');
				touch($log_file);
			}
			elseif(is_file($log_file))
			{
				// если пишем логи сверху, то удалим 
				// и создадим заново пустой файл
				unlink($log_file);
				touch($log_file);
			}
    		}

    		/*
		    проверяем есть ли такой файл
		    если нет - можем ли мы его создать
		    если есть - можем ли мы писать в него
		*/
		if(!is_file($log_file))
		{
			if (!touch($log_file))
			{
				return 'can\'t create log file';
			}
		}
		elseif(!is_writable($log_file))
		{
			return 'can\'t write to log file';
		}

		$fp = fopen($log_file, "a");
		fwrite($fp, $err_str);
		fclose($fp);
	}
}

?>