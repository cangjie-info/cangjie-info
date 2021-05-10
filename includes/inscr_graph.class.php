<?php 

class InscrGraph {
  const LEFTQUOTE = 1;
  const LEFTINNERQUOTE = 2;
  const LEFTTITLE = 4;
  const RIGHTTITLE = 8;
  const PERIOD = 16;
  const QUESTION = 32;
  const EXCLAMATION = 64;
  const LISTCOMMA = 128;
  const COMMA = 256;
  const COLON = 512;
  const RIGHTINNERQUOTE = 1024;
  const RIGHTQUOTE = 2048;
  const TAB = 4096; // (indent, for verse sections, etc. Prepunc.)
  const NEWLINE = 8192; // (start new line, for verse sections, paragraphs in long prose passages or speeches, etc. Prepunc.)
  const SEMICOLON = 16384;

  public int $id = 0;
  public $inscr_id = 0;
  public $number_inscr;
  public int $markup;
  public int $punc;
  public $sentence_id = 0;
  public int $number_sentence;
  public string $graph;
  public int $img_rot = 0;
  public int $img_x = 0;
  public int $img_y = 0;
  public int $img_w = 0;
  public int $img_h = 0;

  public static function charToBit($char) {
    //returns the bit value if $char matches one of the punctuation marks
    if ($char == '“' or $char == '「') return self::LEFTQUOTE;
    if ($char == '‘' or $char == '『') return self::LEFTINNERQUOTE;
    if ($char == '《') return self::LEFTTITLE; 
    if ($char == '》') return self::RIGHTTITLE;
    if ($char == '.' or $char == '。') return self::PERIOD; 
    if ($char == '?' or $char == '？') return self::QUESTION;
    if ($char == '!' or $char == '！') return self::EXCLAMATION;
    if ($char == '、') return self::LISTCOMMA;
    if ($char == '，' or $char == ',') return self::COMMA; 
    if ($char == ';' or $char == '；') return self::SEMICOLON;
    if ($char == ':' or $char == '：') return self::COLON; 
    if ($char == '’' or $char == '』') return self::RIGHTINNERQUOTE;
	 if ($char == '」' or $char == '”') return self::RIGHTQUOTE;
    return 0;
  }

  public static function isPrepunc($char) {
    //return true if string $char is a prepunc character
    if ($char == '“' or $char == '「' or $char == '‘' or $char == '『' or $char == '《') {
      return true;
    }
    return false;
  }

  public static function isPostpunc($char) {
    if ($char == '》'
      or $char == '。'
      or $char == '?' or $char == '？'
      or $char == '!' or $char == '！'
      or $char == '、'
      or $char == '，' or $char == ','
      or $char == ';' or $char == '；'
      or $char == ':' or $char == '：'
      or $char == '’' or $char == '』'
      or $char == '」' or $char == '”') {
      return true;
    }
    return false;
  }

  public function isSentenceFinal() {
    //returns true if punctuation reliably indicates the end of a sentence.
    if ($this->punc & self::PERIOD or 
      $this->punc & self::QUESTION or 
      $this->punc & self::EXCLAMATION or 
      $this->punc & self::COLON or 
      $this->punc & self::SEMICOLON)
      return true;
    else return false;
    }

  public function toString() {
    $prepunc = $this->getPrepuncString();
    $postpunc = $this->getPostpuncString();
	 if(mb_strlen($this->graph) > 1) {
		 return $prepunc . '{' . $this->graph . '}' . $postpunc;
	 }
	 else {
		return $prepunc . $this->graph . $postpunc;
	 }
  }

  private function getPrepuncString() {
    $prepunc = '';
    if ($this->punc &  self::LEFTQUOTE) {$prepunc .= '“';}
    if ($this->punc &  self::LEFTINNERQUOTE) {$prepunc .= '‘';}
    if ($this->punc &  self::LEFTTITLE) {$prepunc .= '《';}
    return $prepunc;
  }

  private function getPostpuncString() {
    $postpunc = '';
    if ($this->punc &  self::RIGHTTITLE) {$postpunc .= '》';}
    if ($this->punc &  self::PERIOD) {$postpunc .= '。';}
    if ($this->punc &  self::QUESTION) {$postpunc .= '？';}
    if ($this->punc &  self::EXCLAMATION) {$postpunc .= '！';}
    if ($this->punc &  self::LISTCOMMA) {$postpunc .= '、';}
    if ($this->punc &  self::COMMA) {$postpunc .= '，';}
    if ($this->punc &  self::SEMICOLON) {$postpunc .= '；';}
    if ($this->punc &  self::COLON) {$postpunc .= '：';}
    if ($this->punc &  self::RIGHTINNERQUOTE) {$postpunc .= '’';}
    if ($this->punc &  self::RIGHTQUOTE) {$postpunc .= '”';}
    return $postpunc;
  }
}
