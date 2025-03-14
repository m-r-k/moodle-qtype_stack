# Matrices and vectors in STACK

This page documents the use of matrices in STACK.  There is a topics page for setting [linear algebra](../Topics/Linear_algebra.md) STACK questions.

## Matrices ##

Note that in Maxima, the operator `.` represents noncommutative multiplication and scalar product. The star `A*B` gives element-wise multiplication.


The following functions are part of Maxima, but are very useful for us.

    rowswap(m,i,j)
    rowadd(m,i,j,k)

Where ` m[i]: m[i] + k * m[j]`.

    rowmul(m,i,k)

Where `m[i]: k * m[i]`.
And a function to compute reduced row echelon form

    rref(m)

(Note the Maxima functions `addrow` and `addcol` appends rows/columns onto the matrix.)

STACK has a library for creating [structured random matrices](Random.md).

## Assigning individual elements ##

To assign values to individual elements, use the simple syntax such as the following.

    m:matrix([1,1],[1,2])
    m[1,2]:3

Note also Maxima's `setelmx` function:

    setelmx (<x>, <i>, <j>, <M>)

Assigns `<x>` to the `(<i>, <j>)`'th element of the matrix `<M>`, and returns the altered matrix. `<M> [<i>, <j>]: <x>` has the same effect, but returns `<x>` instead of `<M>`.


### Showing working {#Showing-working}

It is quite common to want to show part of a matrix calculation "un-evaluated".  For example, the following is typical.

\[ \left[\begin{array}{cc} 1 & 2 \\ 4 & 5 \\ \end{array}\right] + \left[\begin{array}{cc} 1 & -1 \\ 1 & 2 \\ \end{array}\right] = \left[\begin{array}{cc} 1+1 & 2-1 \\ 4+1 & 5+2 \\  \end{array}\right] = \left[\begin{array}{cc} 2 & 1 \\ 5 & 7 \\ \end{array}\right] .\]

This is achieved, by having a question in which simplification is off, and we define the question variables as follows.

    A:matrix([1,2],[4,5]);
    B:matrix([1,-1],[1,2]);
    C:apply(matrix,zip_with(lambda([l1,l2],zip_with("+",l1,l2)),args(A),args(B)));
    D:ev(C,simp);

Notice the use of `zip_with` which is not a core Maxima function, but is defined by STACK.
The above equation is then generated by the CASText

    \[ {@A@}+{@B@}={@C@}={@D@}.\]

A similar procedure is needed for showing working when multiplying matrices.   Here we need to loop over the matrices, for square matrices we use the following.

    A:ev(rand(matrix([5,5],[5,5]))+matrix([2,2],[2,2]),simp);
    B:ev(rand(matrix([5,5],[5,5]))+matrix([2,2],[2,2]),simp);
    BT:transpose(B);
    C:zeromatrix (first(matrix_size(A)), second(matrix_size(A)));
    S:for a:1 thru first(matrix_size(A)) do for b:1 thru second(matrix_size(A)) do C[ev(a,simp),ev(b,simp)]:apply("+",zip_with("*",A[ev(a,simp)],BT[ev(b,simp)]));
    D:ev(C,simp);

Notice we need to simplify the arguments before we take indices of expressions.  This is one problem with `simp:false`.

For non-square matrices we can use this.

    A:ev(rand(matrix([5,5,5],[5,5,5]))+matrix([2,2,2],[2,2,2]),simp);
    B:transpose(ev(rand(matrix([5,5,5],[5,5,5]))+matrix([2,2,2],[2,2,2]),simp));
    TA:ev(A.B,simp);
    BT:transpose(B);
    C:zeromatrix (first(matrix_size(A)), second(matrix_size(B)));
    S:for a:1 thru first(matrix_size(A)) do for b:1 thru second(matrix_size(B)) do C[ev(a,simp),ev(b,simp)]:apply("+",zip_with("*",A[ev(a,simp)],BT[ev(b,simp)]));
    D:ev(C,simp);

Now it makes no sense to include the point wise multiplication of elements as a possible wrong answer.

There must be a more elegant way to do this!

## Display of matrices ## {#matrixparens}

You can set the type of parentheses used to surround matrices in a number of ways.  Firstly, the admin user should set the site default in the qtype_stack options page.

For an individual question, the teacher can set the variable

    lmxchar:"(";

in any of the usual places, e.g. in the question variables.

To set the display of an individual matrix, `m` say, in castext you can use

    {@(lmxchar:"|", m)@}

Since `lmxchar` is a global setting in Maxima, you will have to set it back when you next display a matrix.  Not ideal, but there we are.

Note, STACK only displays matrices with matching parentheses.  If you want something like
\[ f(x) = \left\{ \begin{array}{cc} 1, & x<0 \\ 0, & x\geq 0 \end{array}\right.\]
then you will have to display the matrix without parentheses and sort out the mismatching parentheses in the CASText at the level of display.

For example, if we have the question variable `f:matrix([4*x+4, x<1],[-x^2-4*x-8, x>=1];` and the castext `\[ f(x) := \left\{ {@(lmxchar:"", f)@} \right. \]` STACK generates
\[ f(x) := \left\{ {\begin{array}{cc} 4\cdot x+4 & x < 1 \\ -x^2-4\cdot x-8 & x\geq 1 \end{array}} \right. \]
Notice the use of LaTeX `\left\{` to automatically size the parentheses and `\right.` to represent a matching, but invisible closing parentesis.

## Vectors ## {#vectors}

If you are trying to use the vector notation such as \(3i+4j\) you will probably want to redefine \(i\) to be an abstract symbol, not a complex number.
More information on this is given under [Numbers](Numbers.md).  In particular, use the question level option "Meaning and display of sqrt(-1)" value `symi` to stop interpreting `i` with `i^2=-1` and return it to being an abstract symbol.

Another way to do this is to create matrices as follows:

    ordergreat(i,j,k);
    %_stack_preamble_end;
    p:matrix([-7],[2],[-3]);
    q:matrix([i],[j],[k]);

Now we can use the dot product to create the vector.  The STACK function `texboldatoms` wraps all atomic variable names in the ephemeral function `stackvector`, which is typeset in bold.

    v:texboldatoms(dotproduct(p,q));

If you turn the option "Multiplication sign" to none, this should display as
\[-7\,{\bf{i}}+2\,{\bf{j}}-3\,{\bf{k}}\]
Notice the use of the function `ordergreat`.  `ordergreat` can only be used once at the beginning of the question.

If you use the special constant `%_stack_preamble_end;` then anything before this constant will be available everywhere in the question, including the inputs.

The vector `stackvector(a)` and the atom `a` are different, and are not considered algebraically equivalent.  While students may type in `stackvector(a)` as an answer, they are likely to type in `a`.  The teacher can either (1) add in `stackvector` ephemeral forms to the student's answer in the feedback variables using `texboldatoms` or (2) remove all `stackvector` forms from the teacher's answer by using the `destackvector(ex)` function on their answer.  In the future we may have an option in the input to apply texboldatoms to student's expressions.

The display of the ephemeral form of `stackvector` is controlled by the function `stackvectortex`, e.g. you can display vectors differently using the following examples.

    stackvectortex(ex):= block(sconcat("{\\bf \\vec{", tex1(first(args(ex))), "}}"));
    stackvectortex(ex):= block(sconcat("{\\bf \\underline{", tex1(first(args(ex))), "}}"));

which should go in the question variables.

Any `texput` commands in the question variables now become "context variables" and will be available to the inputs. So, if in the context of your question you would like a variable such as `x` to be considered as a vector and displayed as a vector you can add one of the following to the question variables.

    texput(x, "\\mathbf{\\vec{x}}");
    texput(x, "\\mathbf{\\underline{x}}");

Whenever `x` is then displayed in any part of the question, including the student's input validation or feedback generated by the answer tests, we will have the updated tex for this atom.  E.g.

    texput(x, "\\mathbf{\\vec{x}}");
    texput(y, "\\mathbf{\\vec{y}}");

and a student types in `a*x+b*y` then the tex output will be \(a\cdot \mathbf{\vec{x}}+b\cdot \mathbf{\vec{y}}\).

### Vector cross product ###

The wedge product operator is denoted by the tilde `~`.  This is the `itensor` package.  This package is not normally loaded by STACK, and in any case the package takes lists and not matrices.  For convenience, the following function has been added which requires `3*1` matrices.

    crossproduct(a,b);

Another advantage of this function is the ability to return an un-simplified version with `simp:false`.

## Student input ##

Students do find typing in matrices very tedious.  Some teachers have asked for a convenient notation such as

    c(1,2,3)

for column vectors and

    v(1,2,3,4)

For row vectors.  This is not a core part of STACK currently, but in individual questions you can convert such notation easily into mainstream Maxima using code such as the following.

    ta1:c(1,2,3);
    ta2:v(1,2,3);
    vec_convert(sa) := if op(sa)=c then transpose(matrix(args(sa))) elseif op(sa)=v then matrix(args(sa));
    vec_convert(ta1);
    vec_convert(ta2);

Once converted into Matrices, the student's answer will be evaluated by PRTs as matrices.   Of course, this will not be reflected in the valuation.  If there is sufficient demand for this contact the developers.

