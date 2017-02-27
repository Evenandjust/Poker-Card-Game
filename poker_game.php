<?php 

/*******************************************************************************************************************************
 * 
 * @Written by XZZ
 * @function: Implement a card game as follows:
 * 
 * ========A sample game play scenario========
 * 
 * 0. How many players will be participating this game? (2-4)
 * 1. Game starts with (number of players entered) players
 * 2. Create a new deck of cards
 * 3. Shuffle the deck
 * 4. Deal one card to each player in turns until every player has 5 cards in hand
 * 5. Display what card each player holds
 * 6. Declares the winner
 * 7. Continue? (Y/N)
 * 8. In case the game continues, use the undealt cards to play the next game. When all cards are dealt, reshuffle the whole deck.
 *
 ********************************************************************************************************************************/

// Create a new deck of cards
$ranks = array("2","3","4","5","6","7","8","9","10","J","Q","K","A");

// Spades -> S, Hearts -> H, Diamonds -> D, Clubs -> C 
$suits = array("S","H","D","C");

$deck = new Deck();

$id = $deck->add_card("ranks", $ranks);

$deck->add_card("suits", $suits, 1, $id);

$deck_cards_num = 52;

// Determine whether the game will be running
$game_flag = true;

while($game_flag){

	// Determine whether the user's input is legal
	$initial_flag = true;

	while($initial_flag){
		// Initialize the game with the parameter that the user enters
		fwrite(STDOUT, "\nPlease enter the number of players (2-4): ");
		$num_players = fgets(STDIN);
		
		if($num_players>=2&&$num_players<=4){
			printf("\nGame starts with %d players. \n", $num_players);
			$initial_flag = false;
		}else{
			echo "\nUnexpected Input! Please input again... \n";
		}
	}

	$reset = true;

	// If the remained cards in deck are less than the cards needed to be dealt in this round, then reshuffle the whole deck;
	// else shuffle the remained cards in deck.
	if($deck_cards_num >= 5*$num_players){
		$reset = false;
	}else{
		$reset = true;
		$deck_cards_num = 52;
	}

	// Shuffle the deck
	$deck->shuffle($reset);

	// Deal one card to each player in turns until every player has 5 cards in hand
	echo "\nDealing cards to all the players... \n";

	$hand_arrs = array();
	for($i = 0; $i < 5; $i++){
		for($j = 0; $j < $num_players; $j++){
			$hand_arrs[$j][] = $deck->deal(1);
		}
	}

	echo "\n-----Dealing cards completed----- \n";


	// Display what cards each player holds
	echo "\nNow, display cards each player holds... ( S -> Spades, H -> Hearts, D -> Diamonds, C -> Clubs ) \n";
	echo "\n";

	$index = 1;
	$scores = array();

	foreach($hand_arrs as $hand_arr){

		printf("Player %d : \n\n", $index);

		$card_shown = array();

		foreach ($hand_arr as $item) {
			
			foreach ($item as $key => $value) {
				$card_shown[] = implode("", $value);
			}

		}
		
		switch (determine_score($hand_arr)) {
			case 9:
				echo "Straight Flush: \t";
				break;

			case 8:
				echo "Four Of A Kind: \t";
				break;

			case 7:
				echo "Full House: \t";
				break;

			case 6:
				echo "Flush: \t";
				break;

			case 5:
				echo "Straight: \t";
				break;

			case 4:
				echo "Three Of A Kind: \t";
				break;

			case 3:
				echo "Two Pair: \t";
				break;

			case 2:
				echo "One Pair: \t";
				break;

			case 1:
				echo "High Card: \t";
				break;
		}

		foreach ($card_shown as $item) {
			echo "$item \t";
		}
	    
	    echo "\n\n";

		$scores[] = determine_score($hand_arr);

	    $index++;
	    
	    for($i = 0; $i < count($rank_value);$i++){
	    	echo "rank_value[" . $i . "]=" . $rank_value[$i] . "\n";
		}
	    
	}


	// Compare hands based on scores and declare the winner(s)

	sort($scores);

	$max_score = $scores[$num_players-1];

	$win_index = array();

	for($i = 0; $i < $num_players; $i++) {
		if(determine_score($hand_arrs[$i])===$max_score){
			$win_index[] = $i + 1;
		}
	}

	switch (count($win_index)) {
		case 1:
			echo "Winner is : Player " . $win_index[0] . "\n";
			break;
		
		case 2:
			echo "Tied! Winners are: Player " . $win_index[0] . " and Player " . $win_index[1] . "\n";
			break;

		case 3:
			echo "Tied! Winners are: Player " . $win_index[0] . " , Player " . $win_index[1] . " and Player " . $win_index[2] . "\n";
			break;

		case 4:
			echo "Tied! Winners are: Player " . $win_index[0] . " , Player " . $win_index[1] . " , Player " . $win_index[2] . " and Player " . $win_index[3] . "\n";
			break;

		default:
			echo "Error!";
			break;
	}

	// Update the number of cards in deck
	$deck_cards_num -= 5 * $num_players;

	// Determine whether the user wants to continue the game
	$ans_flag = true;

	while($ans_flag){

		fwrite(STDOUT, "\nDo you want to continue? (Y/N) \n");
		$ans = trim(fgets(STDIN));

		// echo "ans = " . $ans . "\n";

		if($ans === "Y"||$ans === "y"){
			$ans_flag = false;
		}elseif($ans === "N"||$ans === "n"){
			echo "\nBye! \n\n";
			$ans_flag = false;
			$game_flag = false;
		}else{
			echo "\nUnexpected Input! Please input your answer again... \n";
		}

	}
}

// Assign a score to each kind of hands
function determine_score($arr){

	$score = 0;
	if (straight_flush($arr)) {
		$score = 9;
	}elseif (four_of_a_kind($arr)) {
		$score = 8;
	}elseif (full_house($arr)) {
		$score = 7;
	}elseif (flush_kind($arr)) {
		$score = 6;
	}elseif (straight($arr)) {
		$score = 5;
	}elseif (three_of_a_kind($arr)) {
		$score = 4;
	}elseif (two_pair($arr)) {
		$score = 3;
	}elseif (one_pair($arr)) {
		$score = 2;
	}elseif (high_card($arr)) {
		$score = 1;
	}

	return $score;

}

// Determine whether it's straight flush, four of a kind, full house, flush, 
// straight, three of a kind, two pair, one pair, high card
function straight_flush($arr){

	if(straight($arr)&&flush_kind($arr)){
		return 1;
	}else{
		return 0;
	}
}

function four_of_a_kind($arr){

	$rank_value = sort_by_rank_value($arr);

	if($rank_value[0]===$rank_value[1]&&$rank_value[1]===$rank_value[2]&&$rank_value[2]===$rank_value[3]){
		return 1;
	}elseif ($rank_value[1]===$rank_value[2]&&$rank_value[2]===$rank_value[3]&&$rank_value[3]===$rank_value[4]) {
		return 1;
	}else{
		return 0;
	}
}

function full_house($arr){

	$rank_value = sort_by_rank_value($arr);

	if($rank_value[0]===$rank_value[1]&&$rank_value[1]===$rank_value[2]&&$rank_value[2]!==$rank_value[3]&&$rank_value[3]===$rank_value[4]){
		return 1;
	}elseif ($rank_value[2]===$rank_value[3]&&$rank_value[3]===$rank_value[4]&&$rank_value[0]===$rank_value[1]&&$rank_value[1]!==$rank_value[2]) {
		return 1;
	}else{
		return 0;
	}

}

function flush_kind($arr){

	$suit_value = array();

	foreach ($arr as $item) {
		$suit_value[] = $item[0][suits];
	}

	$flag_suit = $suit_value[0];
	$flag = 1;

	for($i = 1; $i < 5; $i++){
		if($suit_value[$i]!==$flag_suit){
			$flag = 0;
			break;
		}else{
			continue;
		}
	}

	return $flag;

}

function straight($arr){
	
	$rank_value = sort_by_rank_value($arr);

	if($rank_value[4]-$rank_value[3]===1&&$rank_value[3]-$rank_value[2]===1&&$rank_value[2]-$rank_value[1]===1&&$rank_value[1]-$rank_value[0]===1){
		return 1;
	}else{
		return 0;
	}
}

function three_of_a_kind($arr){

	$rank_value = sort_by_rank_value($arr);

	if($rank_value[0]===$rank_value[1]&&$rank_value[1]===$rank_value[2]&&$rank_value[2]!==$rank_value[3]&&$rank_value[3]!==$rank_value[4]){
		return 1;
	}elseif ($rank_value[1]===$rank_value[2]&&$rank_value[2]===$rank_value[3]&&$rank_value[0]!==$rank_value[1]&&$rank_value[3]!==$rank_value[4]) {
		return 1;
	}elseif ($rank_value[2]===$rank_value[3]&&$rank_value[3]===$rank_value[4]&&$rank_value[0]!==$rank_value[1]&&$rank_value[1]!==$rank_value[2]) {
		return 1;
	}else{
		return 0;
	}

}

function two_pair($arr){

	$rank_value = sort_by_rank_value($arr);

	if($rank_value[0]===$rank_value[1]&&$rank_value[2]===$rank_value[3]&&$rank_value[1]!==$rank_value[2]&&$rank_value[3]!==$rank_value[4]){
		return 1;
	}elseif ($rank_value[0]===$rank_value[1]&&$rank_value[1]!==$rank_value[2]&&$rank_value[2]!==$rank_value[3]&&$rank_value[3]===$rank_value[4]) {
		return 1;
	}elseif ($rank_value[0]!==$rank_value[1]&&$rank_value[1]===$rank_value[2]&&$rank_value[2]!==$rank_value[3]&&$rank_value[3]===$rank_value[4]) {
		return 1;
	}else{
		return 0;
	}

}

function one_pair($arr){

	$rank_value = sort_by_rank_value($arr);

	if($rank_value[0]===$rank_value[1]&&$rank_value[1]!==$rank_value[2]&&$rank_value[2]!==$rank_value[3]&&$rank_value[3]!==$rank_value[4]){
		return 1;
	}elseif($rank_value[0]!==$rank_value[1]&&$rank_value[1]===$rank_value[2]&&$rank_value[2]!==$rank_value[3]&&$rank_value[3]!==$rank_value[4]){
		return 1;
	}elseif ($rank_value[0]!==$rank_value[1]&&$rank_value[1]!==$rank_value[2]&&$rank_value[2]===$rank_value[3]&&$rank_value[3]!==$rank_value[4]) {
		return 1;
	}elseif ($rank_value[0]!==$rank_value[1]&&$rank_value[1]!==$rank_value[2]&&$rank_value[2]!==$rank_value[3]&&$rank_value[3]===$rank_value[4]) {
		return 1;
	}else{
		return 0;
	}

}

function high_card($arr){

	$rank_value = sort_by_rank_value($arr);

	if($rank_value[0]!==$rank_value[1]&&$rank_value[1]!==$rank_value[2]&&$rank_value[2]!==$rank_value[3]&&$rank_value[3]!==$rank_value[4]&&(!straight($arr))&&(!flush_kind($arr))){
		return 1;
	}else{
		return 0;
	}

}


// Sort the rank values extracted from $arr and store them in $rank_value
function sort_by_rank_value($arr){

	$rank_value = array();

	foreach ($arr as $item) {

		switch($item[0][ranks]){
			case "J":
				$rank_value[] = "11";
				break;
			case "Q":
				$rank_value[] = "12";
				break;
			case "K":
				$rank_value[] = "13";
				break;
			case "A":
				$rank_value[] = "14";
				break;
			default:
				$rank_value[] = $item[0][ranks];
				break;
		}	

	}
	
		
	sort($rank_value);

	return $rank_value;

}


class Deck{

	private $types = array();
	private $deck = array();
	private $count = array();
	
	// Add new card type and property or add properties to the existing card type;
	// $id is the identification of card type;
	// In this case, just consider one card type (2-10,J,Q,K,A), ignore another card type (Jokers).
	public function add_card($property, $prop_names, $num = 1, $id = -1){
		
		$arr = array();

		if($id < 0){
			$index = count($this->types);
		}else{
			$index = $id;
		}

		foreach($prop_names as $key => $value)
		{
			$arr[] = $value;
		}
		if(!isset($this->count[$index]))
		{
			$this->count[$index] = 1;
		}
		$this->count[$index] *= count($arr);
		$this->count[$index] *= $num;
		$this->types[$index][$property] = $arr;
		$this->deck = range(0, array_sum($this->count) - 1);
		return $index;
	}

	// Shuffle function, shuffle the deck according to the $reset value. 
	// $reset = true --> generate all cards; $reset = false --> shuffle cards remained in deck
	public function shuffle($reset = true){
		if($reset)
		{
			$this->deck = range(0, array_sum($this->count) - 1);
		}
		shuffle($this->deck);
	}
	
	// Deal function, in this game, $number = 1
	public function deal($number){
		$arr = array();
		for($i = 1; $i <= $number; $i++)
		{

			// If deck is not empty, then deal the cards. 
			if(!empty($this->deck))
			{
				$cnt = count($arr);
				$card = array_shift($this->deck);
				$sub = 0;
				foreach($this->types as $card_type => $value)
				{
					if($card < $this->count[$card_type] + $sub)
					{
						$mod = 1;
						foreach($value as $key => $val)
						{
							$arr[$cnt][$key] = $val[round(($card - $sub)/$mod) % count($val)];
							$mod *= count($val);
						}
						break;
					}
					else
					{
						$sub += $this->count[$card_type];
					}
				}
			}
			else
			{
				break;
			}
		}
		return $arr;
	}

}

?>