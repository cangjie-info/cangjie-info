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
  const TAB = 4096; // (indent, for verse sections, etc.)
  const NEWLINE = 8192; // (start new line, for verse sections, paragraphs in long prose passages or speeches, etc.)
  const SEMICOLON = 16384;

// SENTENCEPUNC = [PERIOD, QUESTION, EXCLAMATION, COLON, SEMICOLON]

  public $inscr_id;
  public $number_inscr;
  public $markup;
  public $punc;
  public $sentence_id;
  public $number_sentence;
  public $graph;

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

  public static function isPrepunc($mark) {
    //return true if string $mark is a prepunc character
    if ($char == '“' or $char == '「' or $char == '‘' or $char == '『' or $char == '《') {
      return true;
    }
    return false;
  }

  public static function isPostpunc($mark) {
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
    return $prepunc . $this->graph . $postpunc;
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
