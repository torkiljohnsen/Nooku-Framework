<?php
/**
* @version      $Id$
* @category		Koowa
* @package		Koowa_Toolbar
* @subpackage	Button
* @copyright    Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
* @license      GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
*/

/**
 * Delete button class for a toolbar
 *
 * @author		Johan Janssens <johan@nooku.org>
 * @category	Koowa
 * @package		Koowa_Toolbar
 * @subpackage	Button
 */
class KToolbarButtonDelete extends KToolbarButtonPost
{
	public function getOnClick()
	{
		$option	= KRequest::get('get.option', 'cmd');
		$view	= KRequest::get('get.view', 'cmd');
		$token	= JUtility::getToken();
		$json 	= "{method:'post', url:'index.php?option=$option&view=$view&'+id, params:{action:'delete', _token:'$token'}}";

		$msg 	= JText::_('Please select an item from the list');
		return 'var id = KGrid.getIdQuery();'
			.'if(id){new KForm('.$json.').submit();} '
			.'else { alert(\''.$msg.'\'); return false; }';
	}
}