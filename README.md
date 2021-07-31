# juminny-test

The task: https://github.com/jiminny/join-the-team/blob/master/backend-task.md

## Install
Checkout the repo and run `composer install`

## Run

##### Note: The task have two solutions - OOP and one-file non-OOP (kinda POC)

To run OOP solution execute:  
`php analyzer.php`

To run non-OOP solution execute:  
`php analyzer-poc.php`

## Test

Execute `./test.sh`

Included tests:
- test for PSR2
- test for PHP mess (phpmd)
- Unit tests

## Additional note
The value of `user_talk_percentage` cannot be relied with the current data.  
The reason is that there are too much overlapping times between `user` and `customer`.
E.g.:  
The latest point in the dataset is at 1856.77 seconds. 
But the sum of the durations of user and customer talk is 2222.89  
(the user has talked totally 1357.45 seconds, and the customer - 865.44 seconds).
So if we assume (as per task description) that the whole call duration is 1856.77 seconds, this gives us `user_talk_percentage` = 73.11%, which doesn't looks correct.

