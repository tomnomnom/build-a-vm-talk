<?php

const PUSH = 1;
const ADD = 2;
const PRNT = 3;
const HALT = 4;
const JMPLT = 5;

$ops = [
  PUSH => ["push", 1],
  ADD => ["add", 0],
  PRNT => ["print", 0],
  HALT => ["halt", 0],
  JMPLT => ["jmplt", 2],
];


class VM {
  protected $code = [];
  protected $pc = 0;

  protected $stack = [];
  protected $sp = -1; 

  protected function trace(){
    global $ops;

    $addr = $this->pc;
    $op = $ops[$this->code[$this->pc]];
    $stack = implode(" ", array_slice($this->stack, 0, $this->sp+1));
    $args = implode(" ", array_slice($this->code, $this->pc+1, $op[1]));

    printf("%04d: %s %s\t[%s]\n", $addr, $op[0], $args, $stack);
  
  }

  public function run(array $code){
    $this->code = $code; 

    while (true){
      $this->trace();
      $op = $this->code[$this->pc];
      $this->pc++;

      switch ($op){
      
        case PUSH:
          $val = $this->code[$this->pc];
          $this->pc++;

          $this->sp++;
          $this->stack[$this->sp] = $val;
          break;

        case ADD:
          $a = $this->stack[$this->sp];
          $this->sp--;
          $b = $this->stack[$this->sp];
          $this->sp--;

          $this->sp++;
          $this->stack[$this->sp] = $a + $b;
          break;

        case PRNT:
          echo $this->stack[$this->sp].PHP_EOL;
          $this->sp--;
          break;

        case JMPLT:
          $lt = $this->code[$this->pc];
          $this->pc++;

          $addr = $this->code[$this->pc];
          $this->pc++;

          if ($this->stack[$this->sp] < $lt){
            $this->pc = $addr;
          }
          break;

        case HALT:
          return;
      }

    }
  }
}

$vm = new VM();

$vm->run([
  PUSH, 2,
  PUSH, 3,
  ADD,
  JMPLT, 10, 2,
  PRNT,
  HALT,
]);
