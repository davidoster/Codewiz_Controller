<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require( APPPATH . 'libraries/Codewiz/Controller.php' );

class Test extends Codewiz_Controller {

    public function init()
    {
//        $this->load->library( 'calendar', $this->ciCalendarTemplate() );
//        $this->setDataView( array( 
//                    'calendar' => $this->calendar->generate( '' , '' , array(
//                            3  => '<a href="http://example.com/news/article/2006/03/" title="Article 3">Article 3</a>',
//                            7  => '<a href="http://example.com/news/article/2006/07/" title="Article 7">Article 7</a>',
//                            13  => '<a href="http://example.com/news/article/2006/13/" title="Article 13">Article 13</a>',
//                            26  => '<a href="http://example.com/news/article/2006/26/" title="Article 26">Article 26</a>',
//                        )
//                    ),
//                )
//            );
        $this->addRemoteCss("//yui.yahooapis.com/pure/0.2.0/pure-min.css");
    }
    
    public function index()
    {
        $this->setDataLayout( array( 'title' => "CI Testing" ) )
            ->setDataView( array( 'heading' => "Test2" ) )
            ->dispatch();
    }
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */