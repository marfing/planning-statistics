<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CoperturaRepository")
 */
class Copertura
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $idScala;

    /**
     * @ORM\Column(type="integer")
     */
    private $regione;

    /**
     * @ORM\Column(type="integer")
     */
    private $provincia;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $codiceComune;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $frazione;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $particellaTop;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $indirizzo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $civico;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $scalaPalazzina;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $codiceVia;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $idBuilding;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $coordinateBuilding;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pop;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totaleUI;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $statoBuilding;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $statoScalaPalazzina;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $datatRFCIndicativa;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dataRFCEffettiva;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dataRFAIndicativa;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dataRFAEffettiva;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dataUltimaModificaRecord;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dataUltimaModificaStatoBuilding;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dataUltimaVariazioneStatoScalaPalazzina;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Comune", inversedBy="coperture")
     * @ORM\JoinColumn(nullable=false)
     */
    private $comune;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdScala(): ?string
    {
        return $this->idScala;
    }

    public function setIdScala(string $idScala): self
    {
        $this->idScala = $idScala;

        return $this;
    }

    public function getRegione(): ?int
    {
        return $this->regione;
    }

    public function setRegione(int $regione): self
    {
        $this->regione = $regione;

        return $this;
    }

    public function getProvincia(): ?int
    {
        return $this->provincia;
    }

    public function setProvincia(int $provincia): self
    {
        $this->provincia = $provincia;

        return $this;
    }

    public function getCodiceComune(): ?string
    {
        return $this->codiceComune;
    }

    public function setCodiceComune(string $codiceComune): self
    {
        $this->codiceComune = $codiceComune;

        return $this;
    }

    public function getFrazione(): ?string
    {
        return $this->frazione;
    }

    public function setFrazione(?string $frazione): self
    {
        $this->frazione = $frazione;

        return $this;
    }

    public function getParticellaTop(): ?string
    {
        return $this->particellaTop;
    }

    public function setParticellaTop(?string $particellaTop): self
    {
        $this->particellaTop = $particellaTop;

        return $this;
    }

    public function getIndirizzo(): ?string
    {
        return $this->indirizzo;
    }

    public function setIndirizzo(?string $indirizzo): self
    {
        $this->indirizzo = $indirizzo;

        return $this;
    }

    public function getCivico(): ?string
    {
        return $this->civico;
    }

    public function setCivico(?string $civico): self
    {
        $this->civico = $civico;

        return $this;
    }

    public function getScalaPalazzina(): ?string
    {
        return $this->scalaPalazzina;
    }

    public function setScalaPalazzina(?string $scalaPalazzina): self
    {
        $this->scalaPalazzina = $scalaPalazzina;

        return $this;
    }

    public function getCodiceVia(): ?string
    {
        return $this->codiceVia;
    }

    public function setCodiceVia(string $codiceVia): self
    {
        $this->codiceVia = $codiceVia;

        return $this;
    }

    public function getIdBuilding(): ?string
    {
        return $this->idBuilding;
    }

    public function setIdBuilding(string $idBuilding): self
    {
        $this->idBuilding = $idBuilding;

        return $this;
    }

    public function getCoordinateBuilding(): ?string
    {
        return $this->coordinateBuilding;
    }

    public function setCoordinateBuilding(string $coordinateBuilding): self
    {
        $this->coordinateBuilding = $coordinateBuilding;

        return $this;
    }


    public function getPop(): ?string
    {
        return $this->pop;
    }

    public function setPop(?string $pop): self
    {
        $this->pop = $pop;

        return $this;
    }

    public function getTotaleUI(): ?int
    {
        return $this->totaleUI;
    }

    public function setTotaleUI(?int $totaleUI): self
    {
        $this->totaleUI = $totaleUI;

        return $this;
    }

    public function getStatoBuilding(): ?string
    {
        return $this->statoBuilding;
    }

    public function setStatoBuilding(?string $statoBuilding): self
    {
        $this->statoBuilding = $statoBuilding;

        return $this;
    }

    public function getStatoScalaPalazzina(): ?string
    {
        return $this->statoScalaPalazzina;
    }

    public function setStatoScalaPalazzina(?string $statoScalaPalazzina): self
    {
        $this->statoScalaPalazzina = $statoScalaPalazzina;

        return $this;
    }

    public function getDatatRFCIndicativa(): ?\DateTimeInterface
    {
        return $this->datatRFCIndicativa;
    }

    public function setDatatRFCIndicativa(?\DateTimeInterface $datatRFCIndicativa): self
    {
        $this->datatRFCIndicativa = $datatRFCIndicativa;

        return $this;
    }

    public function getDataRFCEffettiva(): ?\DateTimeInterface
    {
        return $this->dataRFCEffettiva;
    }

    public function setDataRFCEffettiva(?\DateTimeInterface $dataRFCEffettiva): self
    {
        $this->dataRFCEffettiva = $dataRFCEffettiva;

        return $this;
    }

    public function getDataRFAIndicativa(): ?\DateTimeInterface
    {
        return $this->dataRFAIndicativa;
    }

    public function setDataRFAIndicativa(?\DateTimeInterface $dataRFAIndicativa): self
    {
        $this->dataRFAIndicativa = $dataRFAIndicativa;

        return $this;
    }

    public function getDataRFAEffettiva(): ?\DateTimeInterface
    {
        return $this->dataRFAEffettiva;
    }

    public function setDataRFAEffettiva(?\DateTimeInterface $dataRFAEffettiva): self
    {
        $this->dataRFAEffettiva = $dataRFAEffettiva;

        return $this;
    }

    public function getDataUltimaModificaRecord(): ?\DateTimeInterface
    {
        return $this->dataUltimaModificaRecord;
    }

    public function setDataUltimaModificaRecord(?\DateTimeInterface $dataUltimaModificaRecord): self
    {
        $this->dataUltimaModificaRecord = $dataUltimaModificaRecord;

        return $this;
    }

    public function getDataUltimaModificaStatoBuilding(): ?\DateTimeInterface
    {
        return $this->dataUltimaModificaStatoBuilding;
    }

    public function setDataUltimaModificaStatoBuilding(?\DateTimeInterface $dataUltimaModificaStatoBuilding): self
    {
        $this->dataUltimaModificaStatoBuilding = $dataUltimaModificaStatoBuilding;

        return $this;
    }

    public function getDataUltimaVariazioneStatoScalaPalazzina(): ?\DateTimeInterface
    {
        return $this->dataUltimaVariazioneStatoScalaPalazzina;
    }

    public function setDataUltimaVariazioneStatoScalaPalazzina(?\DateTimeInterface $dataUltimaVariazioneStatoScalaPalazzina): self
    {
        $this->dataUltimaVariazioneStatoScalaPalazzina = $dataUltimaVariazioneStatoScalaPalazzina;

        return $this;
    }

    public function getComune(): ?Comune
    {
        return $this->comune;
    }

    public function setComune(?Comune $comune): self
    {
        $this->comune = $comune;

        return $this;
    }
}
