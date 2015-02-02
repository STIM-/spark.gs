<?php

  use Silex\Application;
  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\HttpKernel\HttpKernelInterface;


  class Pager {

    var $orm   = null;

    var $show = 15;
    var $line = 10;
    var $group = [];

    var $page  = 0;
    var $total = 0;

    var $fitst = 0;
    var $last  = 0;
    var $prev  = 0;
    var $next  = 0;

    var $gfirst = 0;
    var $glast  = 0;

    var $isFirst  = false;
    var $isLast   = false;

    function __construct ($orm) {

      $this->group['first'] = 1;
      $this->group['last'] = 1;

      $this->orm = $orm;
      $this->page = 1;

      return $this;
    }


    static function factory ($table) {
      return new Pager (Model::factory ($table));
    }


    public function __call ($name, $args) {

      if ($name == 'find_one' || $name == 'find_many' || $name == 'find_array') {

        $this->paging ();

        $this->orm = $this->orm
          ->offset (($this->page - 1) * $this->show)
          ->limit ($this->show);

        return call_user_func_array ([$this->orm, $name], $args);
      }

      $this->orm = call_user_func_array ([$this->orm, $name], $args);

      return $this;
    }


    public function page ($page = null) {
      $this->page = is_null ($page) ? 1 : $page;
      return $this;
    }


    public function show ($show) {
      $this->show = $show;
      return $this;
    }

    public function group ($group) {
      $this->line = $group;
      return $this;
    }

    public function paging () {

      $total = $this->orm->count ();
      $show  = $this->show;
      $page  = $this->page;
      $totalpages = ceil ($total / $show);

      if ($totalpages == 0)
        $totalpages = 1;

      if ($page > $totalpages)
        $page = $totalpages;

      $this->first = 1;
      $this->prev  = $page - 1 <= 1 ? 1 : $page - 1;
      $this->page  = $page;
      $this->next  = $page + 1 >= $totalpages ? $totalpages : $page + 1;
      $this->last  = $totalpages;

      $groupDisplay = $this->line;
      $groupOffset  = 0;

      $groupFirst = $page - 5;

      if ($groupFirst < 1) {
        $groupOffset = abs ($groupFirst) + 1;
        $groupFirst = 1;
      }

      $groupLast = $page + 4 + $groupOffset;

      if ($groupLast > $totalpages) {
        $groupOffset = $groupLast - $totalpages;
        $groupLast = $totalpages;
      }

      if ($groupFirst > $groupOffset)
        $groupFirst -= $groupOffset;

      else
        $groupFirst = 1;

      if ($page == 1)
        $this->isFirst = true;

      if ($page == $totalpages)
        $this->isLast = true;

      $this->group = [
        'first' => $groupFirst,
        'last'  => $groupLast
      ];
    }
  }
