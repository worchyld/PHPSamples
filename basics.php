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

function increment($coins) {
    return $coins += 3;
}

function decrement($coins) {
    return $coins -= 1;
}

function displayCoins($coins) {
    print("Coins: $" . $coins  ."\n");
}