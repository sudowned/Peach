Peach
=====

Peach makes PHP better. With science.

Why?
----

Because PHP makes real Web development far more unpleasant
than it needs to be. Problems like _inconsistent function
names,_ _unreliable argument order_, duck typing, and
_function congestion_ combine to form [a fractal of bad
design](http://me.veekun.com/blog/2012/04/09/php-a-fractal-of-bad-design/) that gets worse and worse
the more you build on it.

Let's talk a little bit more about each of the problems I
mentioned.

**Inconsistent function names**

String functions are named in a slapdash, pseudorandom way.
We've got *addslashes()* (one word,) *get_html_translation_table()*
(underscore separated,) and *htmlspecialchars_decode()* (which
is a block of unseparated words, and then a separated
dongle on the end.) We've got *strrev()*, which is unseparated
and all the vowels sucked out. Point is, whatever you're
trying to do, you need to remember specifically how the
name's been mangled.


**Unreliable argument order**

This one's more insidious, and for a beginner you don't really
notice it until it's four AM and you're swearing at yourself
for transposing the data and the flags for the twentieth
time that night. Let's look at strpos:

    int *strpos* ( string **$haystack**, mixed **$needle** [, int **$offset** = 0 ] )

Makes sense, right? We're taking the **first** argument, and 
**doing stuff to it** with the contents of the second. Great.

But now let's look at **str_replace**:

    mixed str_replace ( mixed **$search** , mixed **$replace** , mixed **$subject** [, int **&$count** ] )

Oh, okay. That's only the **complete opposite.** I'm sure that
won't be hard to remember **at all.**

**Duck Typing**

This one's contentious because there are people who think this
is a good thing. Here's a spoiler: **it is not.**

Have you heard that old saying, "If it walks like a duck and
quacks like a duck, it probably is?" That's how types work in
PHP. If a variable can be used as a number, it's a number. If
it can be used as a string, well, you can bet it's a string.
Same thing goes for booleans and null, because shut up that's
why.

This sounds great at first, but it also means that PHP won't
so much as raise a warning if you feed one of your functions
a string where an integer should go. And then you try to set
`$NumberOfFruit = $NumberOfOranges + $NumberOfGrapes;` and
*NumberOfGrapes* is 6 but the user hit an extra key as they
pressed enter so the server thinks they have `24[ oranges`,
which evaluates to 1 because you're a good developer and used
`intval`, and your application just decided there are 7 fruits
instead of 30 and orders an extra case that you won't know
what to do with.

Thanks, PHP.


**Function Congestion**

There are something like 30 array-related functions in PHP.
Perl has *4.* I don't think anyone's arguing that Perl's a
great role model for much of anything, but we can probably
just write a four-line `while` loop in the time it would take
to look up `array_diff_ukey()` or `array_intersect_uassoc()`
in the manual.


What Peach is
-------------

**Peach is predictable.** It takes arguments in the right order
and it's never hard to figure out what a method is called.
Search a string for a substring with `$String->Contains()`. Search
an array for a value with `$Hash->Contains()`. Search the
array's keys for a value with `$Hash->Keys()->Contains()`.

**Peach is considerate.** It never overwrites arguments. It
never behaves irrationally in order to add "flexibility."

**Peach is vocal.** It throws exceptions when it gets the wrong
types. You'll thank us.


What Peach isn't
----------------

**Peach is not specialized.** It implements what you need to
write solid, object-oriented programs and nothing more. There
is no ORM or database wrappers, but you can use the ones you
already know.

**Peach does not get in the way.** It makes no assumptions,
doesn't rename any existing functions, and doesn't tell you
how to structure your program. Plug it into anything and
start using it. Gets along fine with frameworks, too.

**Peach is not bulky.** It's less than 20k, which is not a
lot of **k**s. 

**Peach is not irrational.** But it will love you back.

**Peach isn't destructive.** It doesn't return false when
something bad happens. It throws an exception so you can
deal with it properly.

**Peach doesn't repeat itself.** Check if a string contains
a substring with `$String->Contains()`. Returns the position
of the substring, too.


Why I did it this way
---------------------

The goal here is to make PHP nicer by exposing functionality
that already exists, but in a better way. There are some
different approaches that could work:

##1) Redefine standard library in the global namespace

This would seem to be the "cleanest" way to do this: we'd
pick a single naming strategy, use function_rename() to rename
any existing functions that have conflicting names, and we
get on with life. Include the lib, use it. Done!

**Pros:** Really easy to grasp. Instead of calling `strlen()`,
call `string_length()`. Instead of `str_replace()`, call
`string_replace()`. You don't have to be an object
oriented coder to grok it.

**Cons** Would break compatibility with existing applications if
existing functions were refactored. Using a lot of functions and
having to type string_ every time sucks and makes for hard to
read code. Doesn't do anything to fix variables. Doesn't let us
change anything in PHP that's a language construct. Doesn't give
us a natural way to provide additional type information.

##2) Present new library in new namespace

This was the second option I considered, and given the
procedural nature of PHP it made a lot of sense, as you can see:

**Pros:** Fairly straightforward to implement. Not inherently
object-oriented, so maybe a better fit for PHP.

**Cons:** The syntax. Oh, wow, the syntax for PHP's namespaces
is just so bad. No one wants to type \peach\string::contains(),
because it's ugly nonsense. Doesn't do anything for types.

##3) Use class instances and mutators

And this is the third option, which you've been reading about.

**Pros:** Instantly readable to anyone who knows Javascript,
which automatically includes 90% of Web developers. Provides
a straightforward mechanism for handling types. Attractive
syntax.

**Cons:** Sort of alien to the PHP paradigm. This is arguably
a good thing. Requires obnoxious getter methods for any type
other than string.

Type Reference
------------

1. String
2. Hash
3. Integer (NIY)
4. Float (NIY)

Integer and Float types are still very, very in progress. I want
there to be a better way to handle type-safe numbers than
`$Int->Add($OtherInt->Get());` or other such nonsense. Currently
I'm looking at implementing a lightweight parser with C `printf`
style variable inclusion, so a math statement would look something
like `$Int = $Int->Calc("@i * @i", $Int, $OtherInt);` which
isn't ideal but is relatively concise.


Method Reference
--------------

What follows is a brief rundown of all Peach's methods and mechanisms.

###Instantiation

####Peach::String(String)
**Arguments:** (1) native PHP string.

Creates a new PeachString. Optionally, a string variable or string literal
may be passed to initialize the PeachString, otherwise it is created empty.

####Peach::Hash(Array)
**Arguments:** (1) native PHP array.

Creates a new PeachHash. If provided in the first argument, a PHP array
will be inserted.


###String Methods
`Contains(String)`
**Supported datatypes:** `(Peach) String`,`String`

**Arguments:** (1) String containing substring to search for
Checks whether the variable contains the data provided.

`Length()`

**Supported datatypes:** `String`, `Hash`

Returns the length of the variable. For hashes, returns the number
of keys.

`Values()`
**Supported datatypes:** `Hash`
Returns a new, numeric-indexed hash of the source hash's values.

`Strip(String)`
**Supported datatypes:** `String`
**Arguments:** (1) String containing all characters to remove
from source string. 
Returns a new string, sans the characters contained in the argument.

`Keys()`
**Supported datatypes:** `Hash`
Returns a new, numeric-indexed hash of the source hash's keys.
