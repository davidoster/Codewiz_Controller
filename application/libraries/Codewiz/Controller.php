<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Codewiz_Controller
 *
 * Codewiz.biz CI_Controller influenced by ZF auto-loading views directory structure while retaining CI's functionality.
 * Includes many features such as a request object holding all arguments sent, controller and method names, http verb, and if ssl.
 * Easily add remote or raw CSS and JS to header or end of html.
 *
 * @package        	Codewiz_Controller
 * @subpackage    	Libraries
 * @category    	Libraries
 * @author        	Christopher Langton <chris@codewiz.biz>
 * @license         GPLv3
 * @link			http://github.com/chrisdlangton/Codewiz_Controller/
 * @link			http://codewiz.biz/
 * @link			http://chrisdlangton.com/
 * @version 		0.0.1
 */
class Codewiz_Controller extends CI_Controller
{
    /**
     * General request data and information.
     *
     * @var object|null
     */
    private $_request = NULL;

    /**
     * The arguments for the GET request method
     *
     * @var array
     */
    protected $_get_args = array();

    /**
     * The arguments for the non-GET request methods
     *
     * @var array
     */
    protected $_args = array();

    /**
     * The data to be passed to the view/s.
     *
     * @var object|null
     */
    protected $_data = NULL;

    /**
     * reserved index for layout data.
     *
     * @var array
     */
    private $reserved = array(
            "jsHeader",
            "jsFooter",
            "remoteJsHeader",
            "remoteJsFooter",
            "css",
            "remoteCss",
        );

    /**
     * Constructor function
     * @todo Document more please.
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->_data = new stdClass();
        foreach ( $this->reserved as $reserved ) $this->_data->layout[$reserved] = array();
        $this->_data->view = array();
        
        $this->_request = new stdClass();
        $this->_request->controller = $this->router->fetch_class();
        $this->_request->method = $this->router->fetch_method();
        $this->_request->ssl = $this->_detect_ssl();
        $this->_request->verb = $this->_detect_method();

        parse_str( file_get_contents( "php://input" ), $this->_args );
        parse_str( $_SERVER['QUERY_STRING'], $this->_get_args );
        $this->_request->args = array_merge( $this->_args , $this->_get_args , $this->uri->ruri_to_assoc() );
        
        if ( ! file_exists( APPPATH . 'views/scripts/' . strtolower( $this->_request->controller ) . '/' . strtolower( $this->_request->method ) . '.php' ) )
	{
            show_404();
	}
        
        $this->init();
        
        return $this;
    }

    /*
     * Init
     *
     * Controllers can extend init to be called directly after __construct
     */
    public function init()
    {
        
    }

    /**
     * set data to the view
     *
     * @param array $data data to pass to the view
     * @return instance Controller
     */
    public function setDataView( array $data = array() )
    {
        $this->_data->view =  array_merge( $this->_data->view , $this->_filterAllowedData( $data ) );
        return $this;
    }
    /**
     * set data to the layout
     *
     * @param array $data data to pass to the layout
     * @return instance Controller
     */
    public function setDataLayout( array $data = array() )
    {
        $this->_data->layout =  array_merge( $this->_data->layout , $this->_filterAllowedData( $data ) );
        return $this;
    }
    /**
     * Dispatch view
     *
     * @param array $data data to apply to all views
     * @param string $view the view name to dispatch
     * @param string $layout the layout view name to dispatch
     */
    public function dispatch( array $data = array() , $view = "" , $layout = "standard" )
    {
        $view = empty( $view ) ? strtolower( $this->_request->method ) : strtolower( $view );
        $this->load->view('layouts/' . $layout . '/header' , $this->_data->layout );
        $this->load->view('scripts/' . strtolower( $this->_request->controller ) . '/' . $view , array_merge( $this->_data->view , $this->_filterAllowedData( $data ) ) );
        
        $this->load->view('layouts/' . $layout . '/footer' , $this->_data->layout );
    }
    /**
     * Retrieve a value from the request arguments, or the whole request object.
     *
     * @param string $key The key for the GET request argument to retrieve
     * @param boolean $xss_clean Whether the value should be XSS cleaned or not.
     * @return string The GET argument value.
     */
    public function prop( $key = NULL, $xss_clean = TRUE )
    {
        if ( $key === NULL )
        {
            return $this->_request;
        }
        return array_key_exists( $key, $this->_request->args ) ? $this->_xss_clean( $this->_request->args[$key], $xss_clean ) : NULL;
    }

    /**
     * add a uri for a remote js resource to the html head
     *
     * @param string $uri uri to remote js resource.
     * @return instance Controller
     */
    public function addRemoteJsHeader( $uri )
    {
        $this->_data->layout['remoteJsHeader'][] = $uri;
        return $this;
    }

    /**
     * add a uri for a remote js resource to the end of the html page
     *
     * @param string $uri uri to remote js resource.
     * @return instance Controller
     */
    public function addRemoteJsFooter( $uri )
    {
        $this->_data->layout['remoteJsFooter'][] = $uri;
        return $this;
    }

    /**
     * add a uri for a remote css resource
     *
     * @param string $uri uri to remote css resource.
     * @return instance Controller
     */
    public function addRemoteCss( $uri )
    {
        $this->_data->layout['remoteCss'][] = $uri;
        return $this;
    }

    /**
     * add raw css script
     *
     * @param string $css raw css text script
     * @return instance Controller
     */
    public function addCss( $css )
    {
        $this->_data->layout['css'][] = $css;
        return $this;
    }

    /**
     * add raw js script to html head
     *
     * @param string $js raw js text script
     * @return instance Controller
     */
    public function addJsToHeader( $js )
    {
        $this->_data->layout['jsHeader'][] = $js;
        return $this;
    }

    /**
     * add raw js script to the end of the html page
     *
     * @param string $js raw js text script
     * @return instance Controller
     */
    public function addJsToFooter( $js )
    {
        $this->_data->layout['jsFooter'][] = $js;
        return $this;
    }

    /**
     * Format an array of data to JSONP string
     *
     * @param array $data The input.
     * @return string
     */
    public function jsonp_format($data = array())
    {
        return $this->get('callback').'('.json_encode($data).')';
    }

    /*
     * Detect SSL use
     *
     * Detect whether SSL is being used or not
     */
    protected function _detect_ssl()
    {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on");
    }

    /**
     * Detect method
     *
     * Detect which HTTP method is being used
     *
     * @return string
     */
    protected function _detect_method()
    {
        return strtolower( $this->input->server('REQUEST_METHOD') );
    }

    /**
     * Detect language(s)
     *
     * What language do they want it in?
     *
     * @return null|string The language code.
     */
    protected function _detect_lang()
    {
        if ( ! $lang = $this->input->server('HTTP_ACCEPT_LANGUAGE') )
        {
            return NULL;
        }
        // They might have sent a few, make it an array
        if ( strpos($lang, ',') !== FALSE )
        {
            $langs = explode(',', $lang);
            $return_langs = array();
            $i = 1;
            foreach ($langs as $lang)
            {
                // Remove weight and strip space
                list($lang) = explode(';', $lang);
                $return_langs[] = trim($lang);
            }
            return $return_langs;
        }
        return $lang;
    }
    /**
     * 
     *
     * @param array $data data to be filtered
     * @return array $allowedData
     */
    protected function _filterAllowedData( array $data = array() )
    {
        $allowedData = array();
        foreach ( $data as $key => $value )
        if ( !in_array( $key , $this->reserved ) )
        $allowedData[$key] = $value;
        return $allowedData;
    }
    /**
     * Process to protect from XSS attacks.
     *
     * @param string $val The input.
     * @param boolean $process Do clean or note the input.
     * @return string
     */
    protected function _xss_clean($val, $process)
    {
        if ( CI_VERSION < 2 )
        {
            return $process ? $this->input->xss_clean($val) : $val;
        }
        return $process ? $this->security->xss_clean($val) : $val;
    }
    /*
     * CI Calendar Template
     *
     * @param bool $big Use the small or large calendar
     * @return string CI Calendar Template
     */
    public function ciCalendarTemplate()
    {
        $config['day_type'] = 'long'; 
        $this->addCss('.calendar {
	font-family: Arial, Verdana, Sans-serif;
	width: 100%;
	min-width: 960px;
	border-collapse: collapse;
}
.calendar tbody tr:first-child th {
	color: #505050;
	margin: 0 0 10px 0;
}
.day_header {
	font-weight: normal;
	text-align: center;
	color: #757575;
	font-size: 10px;
}
.calendar td {
	width: 14%; /* Force all cells to be about the same width regardless of content */
	border:1px solid #CCC;
	height: 100px;
	vertical-align: top;
	font-size: 10px;
	padding: 0;
}
.calendar td:hover {
	background: #F3F3F3;
}
.day_listing {
	display: block;
	text-align: right;
	font-size: 12px;
	color: #2C2C2C;
	padding: 5px 5px 0 0;
}
div.today {
	background: #E9EFF7;
	height: 100%;
}'
            );
        $config['template'] = '{table_open}<table class="calendar">{/table_open}
            {week_day_cell}<th class="day_header">{week_day}</th>{/week_day_cell}
            {cal_cell_content}<span class="day_listing">{day}</span>&nbsp;{content}&nbsp;{/cal_cell_content}
            {cal_cell_content_today}<div class="today"><span class="day_listing">{day}</span>&nbsp;{content}</div>{/cal_cell_content_today}
            {cal_cell_no_content}<span class="day_listing">{day}</span>&nbsp;{/cal_cell_no_content}
            {cal_cell_no_content_today}<div class="today"><span class="day_listing">{day}</span></div>{/cal_cell_no_content_today}';
         return $config;
    }

}
