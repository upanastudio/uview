<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Uview
{
    private $_default_index = 'lihat';
    private $_parser = false;
    private $_view_name = null;
    private $_breadcumb = null;
    private $_js_path = null;
    private $_css_path = null;
    private $_breadcumb_default_class = "kt-subheader__breadcrumbs-link";
    private $_breadcumb_separator = '<span class="kt-subheader__breadcrumbs-separator"></span>'; // bisa diinput html

    // set Instansce Null Object CI
    public $CI = null;

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    /**
     * ajax_response
     *
     * @param  array $data
     * @param  bool $flatten
     *
     * @return json
     */
    public function json_response($data, $flatten = false)
    {
        if ($flatten) {
            $data = $this->_flatten_json($data);
        }

        return $this->CI->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
            ->_display();
        exit();
    }

    /**
     * Function For Generate View Codeiginiter by arhen
     *
     * @param string $view_name this is for view name folder
     * @param array $data_view (Data for store to view)
     * @param string $path_layout (path layout if there is existing)
     * @param string $breadcumb (custom breadcumb)
     * @return void
     */

    public function builder(array $data_view, $title, $path_layout = '', $is_parser = FALSE)
    {
        $trace = debug_backtrace();
        $caller_function = strtolower($trace[1]['function']);
        if ($caller_function == 'index') {
            $caller_function = $this->_default_index;
        }

        //set_view name file path
        if ($this->_view_name)
            $caller_function = $this->_view_name;

        $caller_class = strtolower($trace[1]['class']);

        if ($path_layout != '') {
            $path_layout = strtolower($path_layout) . '/';
        }

        $data = array(); //Set variabel for view
        if (!empty($data_view)) {
            foreach ($data_view as $key => $value) {
                $data[$key] = $value;
            }
        }
        
        $data['title'] = ucwords($title);

        //set_breadcumbs
        if ($this->_breadcumb){

            if (is_array($this->_breadcumb)) {
                
                foreach ($this->_breadcumb as $key => $value) {
                    $this->_breadcumb[$key] = anchor( strtolower($path_layout . $value), ucwords($key), array('class' => $this->_breadcumb_default_class));
                }
                $data['breadcumb'] = $trace[1]['class'] . " &nbsp; {$this->_breadcumb_separator} &nbsp; " . implode(" &nbsp; {$this->_breadcumb_separator} &nbsp; " ,$this->_breadcumb);

            } else {
                // $data['breadcumb'] = ucwords($this->_breadcumb);
                $data['breadcumb'] =  $trace[1]['class'] . " &nbsp; {$this->_breadcumb_separator} &nbsp; " . anchor(current_url().'/#', ucwords($this->_breadcumb), array('class' => $this->_breadcumb_default_class));
            }

        } else {
            $args = '';
            if(! empty($trace[1]['args'])) {
                
                foreach($trace[1]['args'] as $key => $value){
                    $trace[1]['args'][$key] = anchor(current_url().'/#', $value, array('class' => $this->_breadcumb_default_class));
                }
                $args = " &nbsp; {$this->_breadcumb_separator} &nbsp; " . implode(" &nbsp; {$this->_breadcumb_separator} &nbsp; " ,$trace[1]['args']);
            }

            $data['breadcumb'] = anchor(strtolower($path_layout . $trace[1]['class']), ucwords($title), array('class' => $this->_breadcumb_default_class)) . "&nbsp; {$this->_breadcumb_separator} &nbsp; " . anchor( strtolower($path_layout . $trace[1]['class'] . "/{$caller_function}"), ucwords($caller_function), array('class' => $this->_breadcumb_default_class)) . $args;
        }
        
        //set_js
        if($this->_js_path)
            $js_path = $path_layout . $caller_class . '/' . 'js' . '/' . $this->_js_path;
        else
            $js_path = $path_layout . $caller_class . '/' . 'js' . '/' . $caller_function;

        //set_css
        if ($this->_css_path)
            $css_path = $path_layout . $caller_class . '/' . 'css' . '/' . $this->_css_path;
        else
            $css_path = $path_layout . $caller_class . '/' . 'css' . '/' . $caller_function;
        


        $data['isi'] = $path_layout . $caller_class . '/' . $caller_function;
        //optional
        $data['js'] = $this->_is_file_exist($js_path) ? $js_path : '';
        $data['css'] = $this->_is_file_exist($css_path) ? $css_path : '';

        

        $this->_generate($path_layout, $data, $is_parser);

    }

    public function set_filename($view_name){
        $this->_view_name = $view_name;
        return $this;
    }

    public function set_breadcumb($breadcumb){
        $this->_breadcumb = $breadcumb;
        return $this;
    }

    public function set_js_path($js_path){
        $this->_js_path = $js_path;
        return $this;
    }

    public function set_css_path($css_path){
        $this->_css_path = $css_path;
        return $this;
    }

    private function _generate($path_layout, $data, $is_parser)
    {
        if($is_parser === FALSE)
            $this->CI->load->view($path_layout . '_layout/wrapper', $data);
        else{
            $this->CI->load->library('parser');
            $this->CI->parser->parse($path_layout . '_layout/wrapper', $data);
        }


    }

    private function _is_file_exist($file_path)
    {
        $target_file = APPPATH . 'views/' . $file_path . '.php';
        if (! file_exists($target_file)) {
            return false;
        }
        return true;
    }

    public function _flatten_json(array $array)
    {
        $output = array();
        foreach ($array as $v) {
            $output[$v['id']] = $v['nama'];
        }
        return $output;
    }

}
