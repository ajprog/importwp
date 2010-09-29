<?php
if (!isset($gCms)) exit;

#
#The tab headers
#
echo $this->StartTabHeaders();
if (FALSE == empty($params['active_tab']))
  {
    $tab = $params['active_tab'];
  } else {
  $tab = '';
 }
		
if ($this->CheckPermission('Modify CGBlog'))
  {
    echo $this->SetTabHeader('options',$this->Lang('options'), ('options' == $tab)?true:false);
  }
echo $this->EndTabHeaders();

#
#The content of the tabs
#
echo $this->StartTabContent();
if ($this->CheckPermission('Modify CGBlog') )
  {
    echo $this->StartTab('articles', $params);
    include(dirname(__FILE__).'/function.admin_optionstab.php');
    echo $this->EndTab();
  }
echo $this->EndTabContent();

# vim:ts=4 sw=4 noet
?>
