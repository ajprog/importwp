<?php

$messages = '';
if(!isset($gCms->modules["CGBlog"]) || !$gCms->modules["CGBlog"]["active"]){
	$messages .= $this->Lang('error_cgblog');
}
if(!isset($gCms->modules["CGBlog"]) || !$gCms->modules["CGBlog"]["active"]){
	$messages .= $this->Lang('error_cgfeedback');
}

if ($messages != '')
{
	$smarty->assign('messages', $messages);
}
else
{
	  // CreateFormStart sets up a proper form tag that will cause the submit to
	  // return control to this module for processing.
	$smarty->assign('startform', $this->CreateFrontEndFormStart($id,$returnid,'import','post','multipart/form-data'));
	$smarty->assign('endform', $this->CreateFormEnd ());

	$smarty->assign('title_server',$this->Lang('server'));
	$smarty->assign('input_server',$this->CreateInputText($id,'server','',50,255));
	$smarty->assign('title_db_name',$this->Lang('db_name'));
	$smarty->assign('input_db_name',$this->CreateInputText($id,'db_name','',50,255));
	$smarty->assign('title_username',$this->Lang('username'));
	$smarty->assign('input_username',$this->CreateInputText($id,'username','',50,255));
	$smarty->assign('title_password',$this->Lang('password'));
	$smarty->assign('input_password',$this->CreateInputText($id,'password','',50,255));
	$smarty->assign('title_feedback_key',$this->Lang('feedback_key'));
	$smarty->assign('input_feedback_key',$this->CreateInputText($id,'feedback_key','',50,255));
	$smarty->assign('title_prefix',$this->Lang('prefix'));
	$smarty->assign('input_prefix',$this->CreateInputText($id,'prefix','',50,255));
	$smarty->assign('title_file',$this->Lang('file'));
	$smarty->assign('input_file',$this->CreateFileUploadInput($id,'file'));

	$smarty->assign('title_status',$this->Lang('status'));
	$statusdropdown = array();
	$statusdropdown[$this->Lang('draft')] = 'draft';
	$statusdropdown[$this->Lang('published')] = 'published';
	$statusdropdown[$this->Lang('inherit')] = 'inherit';
	$smarty->assign('input_status',
			$this->CreateInputDropdown($id,'status',$statusdropdown,-1));

	$smarty->assign('submit', $this->CreateInputSubmit ($id, 'optionssubmitbutton', $this->Lang('submit')));
}
// Display the populated template
echo $this->ProcessTemplate ('adminprefs.tpl');

?>
