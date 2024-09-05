<?php
// PHP basics
$coins = 1;

displayCoins($coins);
print("\n");
$coins= increment($coins);
displayCoins($coins);
print("\n");
$coins = decrement($coins);
displayCoins($coins);
print("\n");
print square(5);
print("\n");
print_r(small_numbers());

// key value pairs
$details = [
    'id' => 1,
    'name' => 'Alex',
    'email' => "123@fake.st",
 ];

 print_r($details);

function increment($coins) {
    return $coins += 3;
}

function decrement($coins) {
    return $coins -= 1;
}
function square($num) {
    return $num * $num;
}

function small_numbers() {
    return [0,1,2];
}

function displayCoins($coins) {
    print("Coins: $" . $coins  ."\n");
}