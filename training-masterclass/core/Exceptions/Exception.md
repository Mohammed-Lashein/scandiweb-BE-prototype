In the test notion page, they mentioned that the code should be psr compliant , specifically psr-0, psr-4 and psr-12 . 

They didn't mention psr-11 [which has a section regarding container exceptions](https://www.php-fig.org/psr/psr-11/#12-exceptions) , so I will not provide the boilerplate required to implement it . 

Also, the present code is more than enough and also very explicit . 

____
A nice note to mention, if we extended the base Exception class, then we provided a public property of message, on invoking that extending class (our new class) if we didn't provide an exception message, the one we provided in the class as a property will be used . 

I prefer more context specific either error or exception messages so I won't depend on the class's message property . 