<?php

namespace App\Entity;

use App\Repository\FlightsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'flights')]
#[ORM\Entity(repositoryClass: FlightsRepository::class)]
class Flights
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id", nullable: true, options: ["default" => NULL])]
    private ?int $id = NULL;

    #[ORM\Column(name: "flight_id", length: 10, nullable: true, options: ["default" => 'NULL'])]
    private ?string $flightId = 'NULL';

    #[ORM\Column(name: "origin", length: 3, nullable: true, options: ["default" => 'NULL'])]
    private ?string $origin = 'NULL';

    #[ORM\Column(name: "destination", length: 3, nullable: true, options: ["default" => 'NULL'])]
    private ?string $destination = 'NULL';

    #[ORM\Column(name: "departure_time", type: Types::DATETIME_MUTABLE, nullable: true, options: ["default" => 'NULL'])]
    private ?\DateTimeInterface $departureTime = null;

    #[ORM\Column(name: "arrival_time", type: Types::DATETIME_MUTABLE, nullable: true, options: ["default" => 'NULL'])]
    private ?\DateTimeInterface $arrivalTime = null;

    #[ORM\Column(name: "duration", length: 10, nullable: true, options: ["default" => 'NULL'])]
    private ?string $duration = 'NULL';

    #[ORM\Column(name: "carrier_code", length: 10, nullable: true, options: ["default" => 'NULL'])]
    private ?string $carrierCode = 'NULL';

    #[ORM\Column(name: "flight_number", length: 10, nullable: true, options: ["default" => 'NULL'])]
    private ?string $flightNumber = 'NULL';

    #[ORM\Column(name: "aircraft", length: 10, nullable: true, options: ["default" => 'NULL'])]
    private ?string $aircraft = 'NULL';

    #[ORM\Column(name: "stops", nullable: true, options: ["default" => NULL])]
    private ?int $stops = NULL;

    #[ORM\Column(name: "price", type: Types::DECIMAL, precision: 10, scale: 2, nullable: true, options: ["default" => NULL])]
    private ?string $price = 'NULL';

    #[ORM\Column(name: "currency", length: 3, nullable: true, options: ["default" => 'NULL'])]
    private ?string $currency = 'NULL';

    #[ORM\Column(name: "available_seats", nullable: true, options: ["default" => NULL])]
    private ?int $availableSeats = NULL;

    #[ORM\Column(name: "last_ticketing_date", type: Types::DATE_MUTABLE, nullable: true, options: ["default" => 'NULL'])]
    private ?\DateTimeInterface $lastTicketingDate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getFlightId(): ?string
    {
        return $this->flightId;
    }

    public function setFlightId(?string $flightId): static
    {
        $this->flightId = $flightId;

        return $this;
    }

    public function getOrigin(): ?string
    {
        return $this->origin;
    }

    public function setOrigin(?string $origin): static
    {
        $this->origin = $origin;

        return $this;
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function setDestination(?string $destination): static
    {
        $this->destination = $destination;

        return $this;
    }

    public function getDepartureTime(): ?\DateTimeInterface
    {
        return $this->departureTime;
    }

    public function setDepartureTime(?\DateTimeInterface $departureTime): static
    {
        $this->departureTime = $departureTime;

        return $this;
    }

    public function getArrivalTime(): ?\DateTimeInterface
    {
        return $this->arrivalTime;
    }

    public function setArrivalTime(?\DateTimeInterface $arrivalTime): static
    {
        $this->arrivalTime = $arrivalTime;

        return $this;
    }

    public function getDuration(): ?string
    {
        return $this->duration;
    }

    public function setDuration(?string $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getCarrierCode(): ?string
    {
        return $this->carrierCode;
    }

    public function setCarrierCode(?string $carrierCode): static
    {
        $this->carrierCode = $carrierCode;

        return $this;
    }

    public function getFlightNumber(): ?string
    {
        return $this->flightNumber;
    }

    public function setFlightNumber(?string $flightNumber): static
    {
        $this->flightNumber = $flightNumber;

        return $this;
    }

    public function getAircraft(): ?string
    {
        return $this->aircraft;
    }

    public function setAircraft(?string $aircraft): static
    {
        $this->aircraft = $aircraft;

        return $this;
    }

    public function getStops(): ?int
    {
        return $this->stops;
    }

    public function setStops(?int $stops): static
    {
        $this->stops = $stops;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    public function getAvailableSeats(): ?int
    {
        return $this->availableSeats;
    }

    public function setAvailableSeats(?int $availableSeats): static
    {
        $this->availableSeats = $availableSeats;

        return $this;
    }

    public function getLastTicketingDate(): ?\DateTimeInterface
    {
        return $this->lastTicketingDate;
    }

    public function setLastTicketingDate(?\DateTimeInterface $lastTicketingDate): static
    {
        $this->lastTicketingDate = $lastTicketingDate;

        return $this;
    }
}
