<?php
class bookUpdate{
	public $id;
	public $tiddlerName;
	public $updatesTime;
	public $data;
	public $bookName;
	public $systemUpdate;
	public $elemlang;


	public function __construct($id2, $tiddlerName2, $updatesTime2, $data2, $bookName2, $systemUpdate2)
    {
        $this->id = $id2;
	    $this->tiddlerName = $tiddlerName2;
	    $this->updatesTime = $updatesTime2;
	    $this->data = $data2;
        $this->bookName = $bookName2;
	    $this->systemUpdate = $systemUpdate2;

    }
    public function tiddlerMacro(){
        
        return '<<tiddler [['.preg_replace('/_data$/', '' ,$this->tiddlerName).']]>>\\n';
        
    }
}
?>