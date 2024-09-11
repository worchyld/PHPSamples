// rail.swift
enum CardItem {
    case Resource
    case Building
    case Person
    case Utility
}

struct Card {
    let name: String
    let top: Top
    let middle: Middle
    let bottom: Bottom

    struct Top {
        let left: CardItem
        let right: CardItem
    }
    struct Middle {
        let top: CardItem
        let bottomt: CardItem
    }
    let middle: String    
    struct Bottom {
        let left: CardItem
        let right: CardItem
    }
}

let card1 = Card(name: "Hope", 
    top: Card.Top(left: .Resource, right: .Building), 
    middle: "City middle", 
    bottom: Card.Bottom(left: .Person, right: .Utility))

struct Player {
    var cards: [Card]
}

let player = Player(cards: [Card()])
