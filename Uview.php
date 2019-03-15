<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Uview
{
    private $_defaultIndex = 'lihat';

    private $_viewName = null;
    private $_breadcumb = null;
    private $_jsPath = null;
    private $_cssPath = null;

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

    public function builder(array $data_view, $title, $path_layout = '')
    {
        $trace = debug_backtrace();
        $caller_function = strtolower($trace[1]['function']);
        if ($caller_function == 'index') {
            $caller_function = $this->_defaultIndex;
        }

        //set_view name file path
        if ($this->_viewName)
            $caller_function = $this->_viewName;

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
        if ($this->_breadcumb)
            $data['breadcumb'] = ucwords($this->_breadcumb);
        else
            $data['breadcumb'] = ucwords($title . ' - ' . $caller_function);
        
        //set_js
        if($this->_jsPath)
            $js_path = $path_layout . $caller_class . '/' . 'js' . '/' . $this->_jsPath;
        else
            $js_path = $path_layout . $caller_class . '/' . 'js' . '/' . $caller_function;

        //set_css
        if ($this->_cssPath)
            $css_path = $path_layout . $caller_class . '/' . 'css' . '/' . $this->_cssPath;
        else
            $css_path = $path_layout . $caller_class . '/' . 'css' . '/' . $caller_function;
        


        $data['isi'] = $path_layout . $caller_class . '/' . $caller_function;
        //optional
        $data['js'] = $this->_is_file_exist($js_path) ? $js_path : '';
        $data['css'] = $this->_is_file_exist($css_path) ? $css_path : '';

        $this->_generate($path_layout, $data);

    }

    public function set_filename($view_name){
        $this->_viewName = $view_name;
        return $this;
    }

    public function set_breadcumb($breadcumb){
        $this->_breadcumb = $breadcumb;
        return $this;
    }

    public function set_js_path($jspath){
        $this->_jsPath = $jspath;
        return $this;
    }

    public function set_css_path($csspath){
        $this->_cssPath = $csspath;
        return $this;
    }

    private function _generate($path_layout, $data)
    {
        $this->CI->load->view($path_layout . '_layout/wrapper', $data);

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
