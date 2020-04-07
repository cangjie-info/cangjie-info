<?php 

require_once('../includes/all_php.php');
require_once('../includes/db.php');

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

  public function toString() {
    $prepunc = '';
    if ($this->punc &  self::LEFTQUOTE) {$prepunc .= '“';}
    if ($this->punc &  self::LEFTINNERQUOTE) {$prepunc .= '‘';}
    if ($this->punc &  self::LEFTTITLE) {$prepunc .= '《';}
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
    return $prepunc . $this->graph . $postpunc;
  }
}
