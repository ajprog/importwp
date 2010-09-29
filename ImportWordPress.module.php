<?php
#CMS - CMS Made Simple
#(c)2004 by Ted Kulp (wishy@users.sf.net)
#This project's homepage is: http://cmsmadesimple.sf.net
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id: News.module.php 2114 2005-11-04 21:51:13Z wishy $

class ImportWordPress extends CMSModule
{
	function GetName()
	{
		return 'ImportWordPress';
	}

	function GetFriendlyName()
	{
		return $this->Lang('friendlyname');
	}

	function IsPluginModule()
	{
		return false;
	}

	function HasAdmin()
	{
		return true;
	}

	function GetVersion()
	{
		return '0.6.2';
	}

	function MinimumCMSVersion()
	{
		return '1.6.5';
	}

	/*---------------------------------------------------------
	GetDependencies()
	---------------------------------------------------------*/
	function GetDependencies()
	{
		return array('CGBlog'=>'1.4',
			 'CGFeedback'=>'1.1');
	}

	function GetAdminDescription()
	{
		return $this->Lang('description');
	}

	function GetAdminSection()
	{
		return 'extensions';
	}

	function SetParameters()
	{

//		$this->SetParameterType('input_category',CLEAN_STRING);
//		$this->SetParameterType('category_id',CLEAN_INT);
		
//		$this->SetParameterType(CLEAN_REGEXP.'/news_customfield_.*/',CLEAN_STRING);
//		$this->SetParameterType('junk',CLEAN_STRING);

//		$this->mCachable = false;
	}

    function VisibleToAdminUser()
    {
      return $this->CheckPermission('Modify CGBlog');
    }

    function InstallPostMessage()
    {
      return $this->Lang('postinstall');
    }

    function GetHelp($lang='en_US')
    {
      return $this->Lang('help');
    }

    function GetAuthor()
    {
      return 'Jeff Bosch';
    }

    function GetAuthorEmail()
    {
      return 'jeff@ajprogramming.com';
    }

    function GetChangeLog()
    {
      return file_get_contents(dirname(__FILE__).'/changelog.inc');
    }

  function myRedirectToTab( $id, $tab, $params = '' )
  {
    $parms = array();
    if( is_array( $params ) )
      {
	$parms = $params;
      }
    $parms = array('active_tab' => $tab );
    $this->myRedirect( $id, 'defaultadmin', $parms );
  }

  function myRedirect( $id, $action, $params = '' )
  {
    unset( $params['action'] );
    $this->Redirect( $id, $action, '', $params );
  }
}

# vim:ts=4 sw=4 noet
?>
