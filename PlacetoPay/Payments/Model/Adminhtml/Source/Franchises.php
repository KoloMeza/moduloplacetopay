<?php

namespace PlacetoPay\Payments\Model\Adminhtml\Source;

class Franchises
{
    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => 'CDNSA',
                'label' => 'Tarjeta CODENSA'
            ],
            [
                'value' => 'CR_AM',
                'label' => 'American Express'
            ],
            [
                'value' => 'CR_CR',
                'label' => 'Credencial Banco de Occidente',
            ],
            [
                'value' => 'CR_DN',
                'label' => 'Diners Club',
            ],
            [
                'value' => 'CR_VE',
                'label' => 'Visa Electron',
            ],
            [
                'value' => 'CR_VS',
                'label' => 'Visa',
            ],
            [
                'value' => 'DF_DN',
                'label' => 'Datafast Diners',
            ],
            [
                'value' => 'DF_DS',
                'label' => 'Datafast Discover',
            ],
            [
                'value' => 'DF_MC',
                'label' => 'Datafast Mastercard',
            ],
            [
                'value' => 'DF_VS',
                'label' => 'Datafast Visa',
            ],
            [
                'value' => 'DISCO',
                'label' => 'Discover',
            ],
            [
                'value' => 'ID_DN',
                'label' => 'Interdin Diners',
            ],
            [
                'value' => 'ID_DS',
                'label' => 'Interdin Discover',
            ],
            [
                'value' => 'ID_MC',
                'label' => 'Interdin Mastercard',
            ],
            [
                'value' => 'ID_VS',
                'label' => 'Interdin Visa',
            ],
            [
                'value' => 'RM_MC',
                'label' => 'MasterCard',
            ],
            [
                'value' => 'SOMOS',
                'label' => 'Tarjeta SOMOS',
            ],
            [
                'value' => 'TYDAK',
                'label' => 'Tarjeta Alkosto',
            ],
            [
                'value' => 'TYDEX',
                'label' => 'Tarjeta Exito',
            ],
            [
                'value' => 'TS_DN',
                'label' => 'Transerver Diners',
            ],
            [
                'value' => 'TS_DS',
                'label' => 'Transerver Discover',
            ],
            [
                'value' => 'TS_MC',
                'label' => 'Transerver Mastercard',
            ],
            [
                'value' => 'TS_VS',
                'label' => 'Transerver Visa',
            ],
            [
                'value' => 'TSIDN',
                'label' => 'Transerver Intl Diners',
            ],
            [
                'value' => 'TSIDS',
                'label' => 'Transerver Intl Discover',
            ],
            [
                'value' => 'TSIMC',
                'label' => 'Transerver Intl Mastercard',
            ],
            [
                'value' => 'TSIVS',
                'label' => 'Transerver Intl Visa',
            ],
            [
                'value' => 'MT_AM',
                'label' => 'Medianet Amex',
            ],
            [
                'value' => 'MT_DN',
                'label' => 'Medianet Diners',
            ],
            [
                'value' => 'MT_MC',
                'label' => 'Medianet Mastercard',
            ],
            [
                'value' => 'MT_VS',
                'label' => 'Medianet Visa',
            ],
            [
                'value' => 'AT_DN',
                'label' => 'Austro Diners',
            ],
            [
                'value' => 'AT_MC',
                'label' => 'Austro Mastercard',
            ],
            [
                'value' => 'AT_VS',
                'label' => 'Austro Visa',
            ],
            [
                'value' => 'PS_AM',
                'label' => 'Paystudio Amex',
            ],
            [
                'value' => 'PS_DN',
                'label' => 'Paystudio Diners',
            ],
            [
                'value' => 'PS_MC',
                'label' => 'Paystudio Mastercard',
            ],
            [
                'value' => 'PS_VS',
                'label' => 'Paystudio Visa',
            ],
            [
                'value' => 'EB_VS',
                'label' => 'Ebus Visa',
            ],
            [
                'value' => 'EB_MC',
                'label' => 'Ebus Mastercard',
            ],
            [
                'value' => 'EB_AM',
                'label' => 'Ebus Amex',
            ],
            [
                'value' => 'PS_MS',
                'label' => 'Paystudio Maestro',
            ],
            [
                'value' => 'ATHMV',
                'label' => 'ATH-Movil',
            ],
            [
                'value' => 'T1_BC',
                'label' => 'Bancolombia Recaudos',
            ],
            [
                'value' => 'TY_EX',
                'label' => 'Tarjeta Éxito',
            ],
            [
                'value' => 'TY_AK',
                'label' => 'Alkosto',
            ],
            [
                'value' => '_PSE_',
                'label' => 'Débito a cuentas corrientes y ahorros (PSE)',
            ],
            [
                'value' => 'SFPAY',
                'label' => 'Safety Pay',
            ],
            [
                'value' => '_ATH_',
                'label' => 'Corresponsales bancarios Grupo Aval',
            ],
            [
                'value' => 'AC_WU',
                'label' => 'Western Union',
            ],
            [
                'value' => 'PYPAL',
                'label' => 'PayPal',
            ],
            [
                'value' => 'AV_BO',
                'label' => 'Banco de Occidente Recaudos',
            ],
            [
                'value' => 'AV_BP',
                'label' => 'Banco popular Recaudos',
            ],
            [
                'value' => 'AV_AV',
                'label' => 'Banco AV Villas Recaudos',
            ],
            [
                'value' => 'AV_BB',
                'label' => 'Banco de Bogotá Recaudos',
            ],
            [
                'value' => 'VISAC',
                'label' => 'Visa Checkout',
            ],
            [
                'value' => 'GNPIN',
                'label' => 'GanaPIN',
            ],
            [
                'value' => 'GNRIS',
                'label' => 'Tarjeta RIS',
            ],
            [
                'value' => 'MSTRP',
                'label' => 'Masterpass',
            ],
            [
                'value' => 'DBTAC',
                'label' => 'Registro cuentas débito',
            ],
            [
                'value' => '_PPD_',
                'label' => 'Débito pre-autorizado (PPD)',
            ],
            [
                'value' => 'EFCTY',
                'label' => 'Efecty',
            ],
        ];
    }
}
