<?php
/**
 * Licensed under The MIT License (MIT)
 * 
 * Copyright (c) 2013 Michael Schulte
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

class OffsetPaginate 
{
  // Instance variables
  private $queryString = array();
  private $results;
  private $rowsPerPage;
  private $offsetKey;


  public function __construct($queryStrArray, $results, $rpp, $key="start")
  {
    $this->queryString = $queryStrArray;
    $this->results = $results;
    $this->rowsPerPage = $rpp;
    $this->offsetKey = $key;
  }

  private function getCurrentOffset()
  {
  	// offset value from the request query string
    return isset($this->queryString[$this->offsetKey]) ? ((int) $this->queryString[$this->offsetKey]) : 0;
  }

  private function getCurrentPage()
  {
  	// Page number based on current offset value
    return floor( $this->getCurrentOffset() / $this->rowsPerPage ) + 1;
  }

  /**
   * Updates the offset value in a query string
   *
   * @param		array $qstring a query string passed by reference
   * @param		int	$num the new offset key value
   */
  private function setOffset(&$qstring, $num)
  {
    $qstring[$this->offsetKey] = $num;
  }

  private function getOffsetByPage($page)
  {
  	//Caluclates the offset number based on a page
    return $this->rowsPerPage * ($page - 1);
  }

  public static function getNumPages($results, $rpp)
  {
  	// Total number of pages given the number of results and results to display per page
    return ceil($results / $rpp);
  }

  /**
   * Generates an HTML page navigation based on the current page
   *
   * @param		int $pagesToDisplay number of page numbers to display in navigation
   * @return	string HTML anchor tags
   *
   * @todo		create similar methods for different ways of returning navigation results e.g., json, xml
   * @todo		create seperate methods that can just return previous and next page URL
   */
  public function printNavigation($pagesToDisplay=5)
  {

    $pages = $this->getNumPages($this->results, $this->rowsPerPage);
    $query = $this->getQueryString();

	// determine the first page of the current group of $pagesToDisplay
    $firstPage = ( (ceil($this->getCurrentPage() / $pagesToDisplay) ) * $pagesToDisplay) - ($pagesToDisplay - 1);

    // link to previous page and previous grouping if there is one
    if($this->getCurrentPage() > $pagesToDisplay)
    {
      $this->setOffset($query, $this->getOffsetByPage($firstPage - 1) );
      $link = "?" . http_build_query($query);
      $string .= "<a href='$link'> ... </a>";
    }

    // links for the current grouping of $pagesToDisplay
    for ( $i=$firstPage; $i<$pagesToDisplay + $firstPage; $i++)
    {
      $this->setOffset($query, $this->getOffsetByPage($i) );
      $link = "?" . http_build_query($query);
      $string .= "<a href='$link'>$i</a>";
    }

	// link to next page and next grouping if there is one
    if($i < $pages)
    {  
      $this->setOffset($query, $this->getOffsetByPage($i + 1) );
      $link = "?" . http_build_query($query);
      $string .= "<a href='$link'> ... </a>";
    }    
  
  	// return the entire HTML navigation
    return $string;
  }

}//end class OffsetPaginate

?>