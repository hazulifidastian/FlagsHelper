FlagsHelper
===========

PHP Helper for management flags 

Usage
=====

Declare your flags as valid flags on $valid_flags variable

and

FlagsHelper::flags('ORIGINAL')->add('LOCKED')->select();

Above code will inititializing flag ORIGINAL, add flag LOCKED, and then return flags string LOCKED|ORIGINAL
