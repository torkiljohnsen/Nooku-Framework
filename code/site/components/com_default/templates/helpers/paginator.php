<?php
/**
 * @version     $Id$
 * @category    Nooku
 * @package     Nooku_Components
 * @subpackage  Default
 * @copyright   Copyright (C) 2007 - 2010 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Default Paginator Helper
.*
 * @author      Johan Janssens <johan@nooku.org>
 * @category    Nooku
 * @package     Nooku_Components
 * @subpackage  Default
 * @uses        KRequest
 * @uses        KConfig
 */
class ComDefaultTemplateHelperPaginator extends KTemplateHelperPaginator
{
    /**
     * Render item pagination
     * 
     * @param   array   An optional array with configuration options
     * @return  string  Html
     * @see     http://developer.yahoo.com/ypatterns/navigation/pagination/
     */
    public function pagination($config = array())
    { 
        $config = new KConfigPaginator($config);
        $config->append(array(
            'attribs'   => array('onchange' => 'this.form.submit();'),
        ));
         
        return parent::pagination($config);
    }
}