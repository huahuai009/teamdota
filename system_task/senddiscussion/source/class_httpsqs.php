<?php
if(!defined('IN_TEAMDOTA')) {
	exit('Access Denied');
}

class httpsqs{
	function http_get($host, $port, $query)
	{
		$httpsqs_socket = @fsockopen($host, $port, $errno, $errstr, 5);
		if (!$httpsqs_socket)
		{
			return false;
		}
		$out = "GET ${query} HTTP/1.1\r\n";
		$out .= "Host: ${host}\r\n";
		$out .= "Connection: close\r\n";
		$out .= "\r\n";
		fwrite($httpsqs_socket, $out);
		$line = trim(fgets($httpsqs_socket));
		$header = $line;
		list($proto, $rcode, $result) = explode(" ", $line);
		$len = -1;
		while (($line = trim(fgets($httpsqs_socket))) != "")
		{
			$header .= $line;
			if (strstr($line, "Content-Length:"))
			{
				list($cl, $len) = explode(" ", $line);
			}
			if (strstr($line, "Pos:"))
			{ 
				list($pos_key, $pos_value) = explode(" ", $line);
			}
		}
		if ($len < 0)
		{
			return false;
		}
		$body = @fread($httpsqs_socket, $len);
		fclose($httpsqs_socket);
		$result_array["pos"] = (int)$pos_value;
		$result_array["data"] = $body;
		return $result_array;
	}
	function http_post($host, $port, $query, $body)
	{
		$httpsqs_socket = @fsockopen($host, $port, $errno, $errstr, 5);
		if (!$httpsqs_socket)
		{
			return false;
		}
		$out = "POST ${query} HTTP/1.1\r\n";
		$out .= "Host: ${host}\r\n";
		$out .= "Content-Length: " . strlen($body) . "\r\n";
		$out .= "Connection: close\r\n";
		$out .= "\r\n";
		$out .= $body;
		fwrite($httpsqs_socket, $out);
		$line = trim(fgets($httpsqs_socket)); 
		$header = $line;
		list($proto, $rcode, $result) = explode(" ", $line);
		$len = -1;
		while (($line = trim(fgets($httpsqs_socket))) != "")
		{
			$header .= $line;
			if (strstr($line, "Content-Length:"))
			{
				list($cl, $len) = explode(" ", $line);
			}
			if (strstr($line, "Pos:"))
			{
				list($pos_key, $pos_value) = explode(" ", $line);
			}
		}
		if ($len < 0)
		{
			return false;
		}
		$body = @fread($httpsqs_socket, $len);
		fclose($httpsqs_socket);
		$result_array["pos"] = (int)$pos_value;
		$result_array["data"] = $body;
		return $result_array;
	}
	/* ------------------ http connect without Keep-Alive(Use in Webserver)------------------------ */
	function put($host, $port, $charset='utf-8', $name, $data)
	{
		$result = $this->http_post($host, $port, "/?charset=".$charset."&name=".$name."&opt=put", $data);
		if ($result["data"] == "HTTPSQS_PUT_OK")
		{ 
			return true;
		} 
		else if ($result["data"] == "HTTPSQS_PUT_END")
		{
			return $result["data"];
		}
		return false;
	}
	//出队列(从队列中取出文本消息)
	function get($host, $port, $charset='utf-8', $name)
	{
		$result = $this->http_get($host, $port, "/?charset=".$charset."&name=".$name."&opt=get");
		if ($result == false || $result["data"] == "HTTPSQS_ERROR" || $result["data"] == false)
		{
			return false;
		}
		return $result["data"];
	} 
	function gets($host, $port, $charset='utf-8', $name)
	{
		$result = $this->http_get($host, $port, "/?charset=".$charset."&name=".$name."&opt=get");
		if ($result == false || $result["data"] == "HTTPSQS_ERROR" || $result["data"] == false)
		{
			return false;
		}
		return $result;
	}
	//查看队列状态（普通方式，便于浏览器查看）
	//如果“队列写入点值”大于“最大队列数量值”，将重置“队列写入点”为1，即又从1开始存储新的队列内容，覆盖原来队列位置点的内容
	function status($host, $port, $charset='utf-8', $name)
	{
		$result = $this->http_get($host, $port, "/?charset=".$charset."&name=".$name."&opt=status");
		if ($result == false || $result["data"] == "HTTPSQS_ERROR" || $result["data"] == false)
		{
			return false;
		}
		return $result["data"];
	}
	/*查看指定队列位置点的内容
	跟一般的队列系统不同的是，HTTPSQS可以查看指定队列ID（队列点）的内容，包括未出、已出的队列内容。
	可以方便地观测进入队列的内容是否正确。另外，假设有一个发送手机短信的队列，由客户端守护进程从队列中取出信息，
	并调用“短信网关接口”发送短信。但是，如果某段时间“短信网关接口”有故障，而这段时间队列位置点300~900的信息已经出队列，
	但是发送短信失败，我们还可以在位置点300~900被覆盖前，查看到这些位置点的内容，作相应的处理。
	pos >=1 并且 <= 1000000000,返回指定队列位置点的内容。*/
	function view($host, $port, $charset='utf-8', $name, $pos)
	{
		$result = $this->http_get($host, $port, "/?charset=".$charset."&name=".$name."&opt=view&pos=".$pos);
		if ($result == false || $result["data"] == "HTTPSQS_ERROR" || $result["data"] == false)
		{
			return false;
		}
		return $result["data"];
	}
	//重置指定队列
	function reset($host, $port, $charset='utf-8', $name)
	{
		$result = $this->http_get($host, $port, "/?charset=".$charset."&name=".$name."&opt=reset");
		if ($result["data"] == "HTTPSQS_RESET_OK")
		{
			return true;
		}
		return false;
	}
	/*更改指定队列的最大队列数量：
	默认的最大队列长度（100万条）：1000000
	更改的最大队列数量必须大于当前的“队列写入点”。另外，当“队列写入点”小于“队列读取点”时（即PUT位于圆环的第二圈，而GET位于圆环的第一圈时），
	本操作将被取消，然后返回给客户端以下信息：*/
	function maxqueue($host, $port, $charset='utf-8', $name, $num)
	{
		$result = $this->http_get($host, $port, "/?charset=".$charset."&name=".$name."&opt=maxqueue&num=".$num);
		if ($result["data"] == "HTTPSQS_MAXQUEUE_OK")
		{
			return true;
		}
		return false;
	}
	//查看队列状态（JSON方式，便于程序处理返回内容）
	//返回（示例）：{"name":"xoyo","maxqueue":1000000,"putpos":45,"putlap":1,"getpos":6,"getlap":1,"unread":39}
	function status_json($host, $port, $charset='utf-8', $name)
	{
		$result = $this->http_get($host, $port, "/?charset=".$charset."&name=".$name."&opt=status_json");
		if ($result == false || $result["data"] == "HTTPSQS_ERROR" || $result["data"] == false) 
		{
			return false;
		}
		return $result["data"];
	}
	//不停止服务的情况下，修改定时刷新内存缓冲区内容到磁盘的间隔时间
	//默认间隔时间：5秒 或 httpsqs -s <second> 参数设置的值。
	//num >=1 and <= 1000000000
	function synctime($host, $port, $charset='utf-8', $name, $num)
	{
		$result = $this->http_get($host, $port, "/?charset=".$charset."&name=".$name."&opt=synctime&num=".$num);
		if ($result["data"] == "HTTPSQS_SYNCTIME_OK")
		{
			return true;
		}
		return false;
	}
}
?>
