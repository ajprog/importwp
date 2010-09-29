<?php

function xml2assoc($xml) {
    $tree = null;
    while($xml->read())
        switch ($xml->nodeType) {
            case XMLReader::END_ELEMENT: return $tree;
            case XMLReader::ELEMENT:
                $node = array('tag' => $xml->name, 'value' => $xml->isEmptyElement ? '' : xml2assoc($xml));
                if($xml->hasAttributes)
                    while($xml->moveToNextAttribute())
                        $node['attributes'][$xml->name] = $xml->value;
                $tree[] = $node;
            break;
            case XMLReader::TEXT:
            case XMLReader::CDATA:
                $tree .= $xml->value;
        }
    return $tree;
} 

if (!isset($gCms)) exit;

if( !$this->CheckPermission( 'Modify CGBlog' ) )
{
  return;
}
//debug_display($_FILES);
$fieldName = $id."file";
if (!isset ($_FILES[$fieldName]) || !isset ($_FILES)
|| !is_array ($_FILES[$fieldName]) || !$_FILES[$fieldName]['name'])
{
	echo "Error Uploading the File.";
	return false;
}
else
{
	// normalize the file variable
	$file = $_FILES[$fieldName];

	// $file['tmp_name'] is the file we have to parse
	$xml = new XMLReader();
	$xml->open($file['tmp_name']); 
	//$xml = file_get_contents( $file['tmp_name'] );

	// start parsing xml
	$val = xml2assoc($xml);
	$xml->close(); 

	//debug_display($val);

	//echo "Level 1: ".count($val)."<br/>";
	//echo "Level 2: ".count($val[0]['value'])."<br/>";
	//echo "Level 3: ".count($val[0]['value'][0]['value'])."<br/>";
	$import_post = array();
	foreach ($val[0]['value'][0]['value'] as $elements) 
	{
		switch ($elements['tag'])
		{
			case 'item':
				$post = array();
				foreach ($elements['value'] as $attr)
				{
					switch ($attr['tag'])
					{
						case 'wp:post_type':
							$post['type'] = $attr['value'];
							break;
						case 'pubDate':
							$post['date'] = strtotime($attr['value']);
							break;
						case 'dc:creator':
							$post['author'] = $attr['value'];
							break;
						case 'link':
							$post['link'] = $attr['value'];
							break;
						case 'category':
							if ( isset($attr['attributes']) && $attr['attributes']['domain'] == 'category')
							{
								$post['category'][] = array( $attr['attributes']['nicename'] => $attr['value']);
							}
							break;
						case 'wp:comment':
							$comment = array();
							foreach ($attr['value'] as $wp_comment)
							{
								switch ($wp_comment['tag'])
								{
									case 'wp:comment_author':
										$comment['author'] = $wp_comment['value'];
										break;
									case 'wp:comment_author_email':
										$comment['email'] = $wp_comment['value'];
										break;
									case 'wp:comment_author_IP':
										$comment['author_ip'] = $wp_comment['value'];
										break;
									case 'wp:comment_author_url':
										$comment['author_url'] = $wp_comment['value'];
										break;
									case 'wp:comment_date':
										$comment['date'] = strtotime($wp_comment['value']);
										break;
									case 'wp:comment_content':
										$comment['content'] = $wp_comment['value'];
										break;
									case 'wp:comment_approved':
										$comment['status'] = $wp_comment['value'];
										break;
									default:
										break;
								}
							}
							$post['comments'][] = $comment;
							break;
						case 'content:encoded':
							$post['content'] = $attr['value'];
							break;
						case 'excerpt:encoded':
							$post['summary'] = $attr['value'];
							break;
						case 'wp:post_date':
							$post['post_date'] = strtotime($attr['value']);
							break;
						case 'wp:status':
							$post['status'] = $attr['value'];
							break;
						case 'wp:post_id':
							$post['id'] = $attr['value'];
							break;
						case 'wp:post_parent':
							$post['parent'] = $attr['value'];
							break;
						case 'wp:post_name':
							$post['post_name'] = $attr['value'];
							break;
						case 'title':
							$post['title'] = $attr['value'];
							break;
						default:
							break;
					}
				}
				if ($post['type'] == 'post')
				{
					$import_post[] = $post;
				}
				break;
			default:
				break;
		}
	}
	//unset($val[0]);

	//debug_display($import_post);

	$iquery = 'INSERT INTO '.cms_db_prefix().'module_cgblog (cgblog_id, cgblog_title, cgblog_data, summary, status, cgblog_date, start_time, end_time, create_date, modified_date,author,cgblog_extra,url) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)';

	$icquery = 'INSERT INTO '.cms_db_prefix().'module_cgfeedback_comments (key1,key2,key3,rating,title,data,status,
                   author_name,author_email,author_ip,author_notify,created,modified)
                  VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)';

	foreach ($import_post as $row)
	{
		if ( $params['status'] != 'inherit' && $row['status'] != 'publish' )
		{
			continue;
		}
		echo "<br>Article: ".$row['title'];
		$articleid = $db->GenID(cms_db_prefix()."module_cgblog_seq");

		if ($params['status'] == 'inherit')
		{
			if ( $row['status'] == 'publish' )
			{
				$status = 'published';
			}
			else
			{
				$status = 'draft';
			}
		}
		else
		{
			$status = $params['status'];
		}
		$dbr = $db->Execute($iquery, array($articleid, $row['title'], $row['content'], $row['summary'], $status, trim($db->DBTimeStamp($row['post_date']), "'"), trim($db->DBTimeStamp($row['post_date']), "'"), NULL, trim($db->DBTimeStamp($row['date']), "'"), trim($db->DBTimeStamp($row['date']), "'"), $row['author'],'', strftime('%Y/%m/%d/',$row['post_date']).$row['post_name']));

		if( !$dbr )
		{
			echo "DEBUG: SQL = ".$db->sql."<br/>";
			die($db->ErrorMsg());
		}
		if( isset($row['comments']) && count($row['comments']) != 0 )
		{
			foreach ($row['comments'] as $row2)
			{
				if ($row2['status'] == 'spam')
				{
					continue;
				}
				echo "<br>Comment: ".$row2['content'];
				$dbr3 = $db->Execute($icquery, array($params['feedback_key'],$articleid,'',0,$row2['author_url'],
						$row2['content'],'published', $row2['author'],$row2['email'],
						$row2['author_ip'],0,trim($db->DBTimeStamp($row2['date']), "'"),
						trim($db->DBTimeStamp($row2['date']), "'")));
				if( !$dbr )
				{
					echo "DEBUG: SQL = ".$db->sql."<br/>";
					die($db->ErrorMsg());
				}
			}
		}
	}
}

?>
