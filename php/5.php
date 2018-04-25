<?php

// Challenge: refactor these interfaces into a more sensible architecture (adding new interfaces where required)

interface AirportInterface
{
	public static function getAll();
	public function getDestinations();
	public function getDepartureTimes(AirportInterface $destination);
}

interface Flight
{
	public function getCost(AirportInterface $origin, AirportInterface $destination, UserInterface $user, $time);
	public function book(AirportInterface $origin, AirportInterface $destination, UserInterface $user, $time, $cost);
}


interface UserInterface {}
