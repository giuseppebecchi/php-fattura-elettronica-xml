<?php
/**
 * Created by    Gabriel Andrei gabrielandrei79@gmail.com
 * Date:         03/11/2018
 * Time:         18:56
 */

namespace Advinser\FatturaElettronicaXml;

use Advinser\FatturaElettronicaXml\Body\FatturaElettronicaBody;
use Advinser\FatturaElettronicaXml\Header\CedentePrestatore;
use Advinser\FatturaElettronicaXml\Header\CessionarioCommittente;
use Advinser\FatturaElettronicaXml\Header\FatturaElettronicaHeader;


class FatturaElettronica
{
    /**
     * @var FatturaElettronicaHeader
     */
    private $header;
    /**
     * @var FatturaElettronicaBody[]
     */
    private $bodys;

    /**
     * @var string
     */
    private $versione;

    /**
     * @var bool
     */
    private $lotto = false;

    /**
     * FatturaElettronica constructor.
     * @param FatturaElettronicaHeader $header
     * @param FatturaElettronicaBody $body
     */
    public function __construct(FatturaElettronicaHeader $header = null, FatturaElettronicaBody $body = null)
    {
        $this->header = $header;
        $this->bodys = $body;

        if ($header instanceof FatturaElettronicaHeader) {
            $this->versione = $header->getDatiTrasmissione()->getFormatoTrasmissione();
        }
    }

    /**
     * @return Structures\Fiscale|null
     */
    public function getPivaCedente()
    {
        if ($this->getHeader() instanceof FatturaElettronicaHeader) {
            if ($this->getHeader()->getCedentePrestatore() instanceof CedentePrestatore) {
                return $this->getHeader()->getCedentePrestatore()->getIdFiscaleIVA();
            }
        }
        return null;
    }

    /**
     * @return Structures\Fiscale|null
     */
    public function getPivaCommittente()
    {
        if ($this->getHeader() instanceof FatturaElettronicaHeader) {
            if ($this->getHeader()->getCessionarioCommittente() instanceof CessionarioCommittente) {
                return $this->getHeader()->getCessionarioCommittente()->getIdFiscaleIVA();
            }
        }
        return null;
    }

    /**
     * @return null|string
     */
    public function getCodiceDestinatario()
    {
        if ($this->getHeader() instanceof FatturaElettronicaHeader) {
            if ($this->getHeader()->getCessionarioCommittente() instanceof CessionarioCommittente) {
                return $this->getHeader()->getDatiTrasmissione()->getCodiceDestinatario();
            }
        }
        return null;
    }

    /**
     * @return null|string
     * @throws FatturaElettronicaException
     */
    public function getNumeroFattura(){
        if($this->isLotto()){
            throw new FatturaElettronicaException("There are multiple body, that means that is non a single 'FatturaElettronica', but a 'Lotto di fatture'. 'Numero fattura' can't be returned safely");
        }
        return $this->getBodys()[0]->getDatiGenerali()->getDatiGeneraliDocumento()->getNumero();
    }

    /**
     * @return null|string
     * @throws FatturaElettronicaException
     */
    public function getDataFattura(){
        if($this->isLotto()){
            throw new FatturaElettronicaException("There are multiple body, that means that is non a single 'FatturaElettronica', but a 'Lotto di fatture'. 'Numero fattura' can't be returned safely");
        }
        return $this->getBodys()[0]->getDatiGenerali()->getDatiGeneraliDocumento()->getData();
    }

    /**
     * @return null|string
     * @throws FatturaElettronicaException
     */
    public function getImportoTotaleDocumento(){
        if($this->isLotto()){
            throw new FatturaElettronicaException("There are multiple body, that means that is non a single 'FatturaElettronica', but a 'Lotto di fatture'. 'ImportoTotaleDocumento' can't be returned safely");
        }

        return $this->getBodys()[0]->getDatiGenerali()->getDatiGeneraliDocumento()->getImportoTotaleDocumento();

    }

    /**
     * @return null|string
     * @throws FatturaElettronicaException
     */
    public function getImponibileImporto(){
        if($this->isLotto()){
            throw new FatturaElettronicaException("There are multiple body, that means that is non a single 'FatturaElettronica', but a 'Lotto di fatture'. 'ImponibileImporto' can't be returned safely");
        }
        return $this->getBodys()[0]->getDatiBeniServizi()->getImponibileImporto();
    }

    /**
     * @return null|string
     * @throws FatturaElettronicaException
     */
    public function getImposta(){
        if($this->isLotto()){
            throw new FatturaElettronicaException("There are multiple body, that means that is non a single 'FatturaElettronica', but a 'Lotto di fatture'. 'Imposta' can't be returned safely");
        }
        return $this->getBodys()[0]->getDatiBeniServizi()->getImposta();
    }

    /**
     * @return null|string
     * @throws FatturaElettronicaException
     */
    public function getAliquotaIVA(){
        if($this->isLotto()){
            throw new FatturaElettronicaException("There are multiple body, that means that is non a single 'FatturaElettronica', but a 'Lotto di fatture'. 'AliquotaIVA' can't be returned safely");
        }
        return $this->getBodys()[0]->getDatiBeniServizi()->getAliquotaIVA();
    }

    /**
     * @return bool
     */
    public function isLotto(): bool
    {
        if (count($this->getBodys()) > 1) {
            $this->lotto = true;
        }
        return $this->lotto;
    }

    /**
     * @param bool $lotto
     * @return FatturaElettronica
     */
    public function setLotto(bool $lotto): FatturaElettronica
    {
        $this->lotto = $lotto;
        return $this;
    }


    /**
     * @return FatturaElettronicaHeader
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @param FatturaElettronicaHeader $header
     * @return FatturaElettronica
     */
    public function setHeader($header)
    {
        $this->header = $header;
        $this->versione = $header->getDatiTrasmissione()->getFormatoTrasmissione();
        return $this;
    }

    /**
     * @return FatturaElettronicaBody[]
     */
    public function getBodys(): array
    {
        return $this->bodys;
    }

    /**
     * @param FatturaElettronicaBody[] $bodys
     * @return FatturaElettronica
     */
    public function setBodys(array $bodys): FatturaElettronica
    {
        $this->bodys = $bodys;
        return $this;
    }


    /**
     * @param FatturaElettronicaBody $body
     * @return FatturaElettronica
     */
    public function addBody(FatturaElettronicaBody $body): FatturaElettronica
    {
        $this->bodys[] = $body;
        if(count($this->bodys)>1){
            $this->setLotto(true);
        }
        return $this;
    }


    /**
     * @return array
     * @throws FatturaElettronicaException
     */
    public function toArray()
    {
        if (!$this->header instanceof FatturaElettronicaHeader) {
            throw new FatturaElettronicaException("missed istance of FatturaElettronicaHeader");
        }
        $array = [
            '@versione' => $this->versione,
            '@xmlns:ds' => 'http://www.w3.org/2000/09/xmldsig#',
            '@xmlns:p' => 'http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2',
            '@xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
            '@xsi:schemaLocation' => 'http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2 http://www.fatturapa.gov.it/export/fatturazione/sdi/fatturapa/v1.2/Schema_del_file_xml_FatturaPA_versione_1.2.xsd',
            'FatturaElettronicaHeader' => $this->header->toArray(),
            'FatturaElettronicaBody' => null,
        ];

        if (count($this->getBodys()) == 0) {
            throw new FatturaElettronicaException("missed istance of FatturaElettronicaBody");
        } else {
            if ($this->isLotto()) {
                foreach ($this->getBodys() as $body) {
                    $array['FatturaElettronicaBody'][] = $body->toArray();
                }
            } else {
                $array['FatturaElettronicaBody'] = $this->getBodys()[0]->toArray();
            }
        }

        return $array;
    }

    /**
     * @param array $array
     * @return FatturaElettronica
     * @throws FatturaElettronicaException
     */
    public static function fromArray(array $array)
    {
        $o = new FatturaElettronica();

        if (!empty($array['FatturaElettronicaHeader'])) {
            $o->setHeader(FatturaElettronicaHeader::fromArray($array['FatturaElettronicaHeader']));
        }
        if (!empty($array['FatturaElettronicaBody'])) {
            if (isset($array['FatturaElettronicaBody'][0])) {
                foreach ($array['FatturaElettronicaBody'] as $item) {
                    $o->addBody(FatturaElettronicaBody::fromArray($item));
                }
            } else {
                $o->addBody(FatturaElettronicaBody::fromArray($array['FatturaElettronicaBody']));
            }
        }

        return $o;
    }

    /**
     * @param string $json
     * @return FatturaElettronica
     * @throws FatturaElettronicaException
     */
    public static function fromJson(string $json)
    {
        return FatturaElettronica::fromArray(json_decode($json,true));
    }


}