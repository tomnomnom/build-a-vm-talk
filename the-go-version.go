package main

import "fmt"

const (
	PUSH = iota
	ADD
	PRINT
	HALT
	JMPLT
)

type op struct {
	name  string
	nargs int
}

var ops = map[int]op{
	PUSH:  op{"push", 1},
	ADD:   op{"add", 0},
	PRINT: op{"print", 0},
	HALT:  op{"halt", 0},
	JMPLT: op{"jmplt", 2},
}

type VM struct {
	code []int
	pc   int

	stack []int
	sp    int
}

func (v *VM) trace() {
	addr := v.pc
	op := ops[v.code[v.pc]]
	args := v.code[v.pc+1 : v.pc+op.nargs+1]
	stack := v.stack[0 : v.sp+1]

	fmt.Printf("%04d: %s %v \t%v\n",
		addr, op.name, args, stack)
}

func (v *VM) run(c []int) {

	v.stack = make([]int, 100)
	v.sp = -1

	v.code = c
	v.pc = 0

	for {
		v.trace()

		//Fetch
		op := v.code[v.pc]
		v.pc++

		// Decode
		switch op {
		case PUSH:
			val := v.code[v.pc]
			v.pc++

			v.sp++
			v.stack[v.sp] = val
		case ADD:
			a := v.stack[v.sp]
			v.sp--
			b := v.stack[v.sp]
			v.sp--

			v.sp++
			v.stack[v.sp] = a + b
		case PRINT:
			val := v.stack[v.sp]
			v.sp--
			fmt.Println(val)
		case JMPLT:
			lt := v.code[v.pc]
			v.pc++
			addr := v.code[v.pc]
			v.pc++

			if v.stack[v.sp] < lt {
				v.pc = addr
			}
		case HALT:
			return
		}
	}

}

func main() {

	code := []int{
		PUSH, 2,
		PUSH, 3,
		ADD,
		JMPLT, 10, 2,
		PRINT,
		HALT,
	}

	v := &VM{}
	v.run(code)
}
