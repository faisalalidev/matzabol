<?php

namespace App\Http\Controllers\Api;

use App\Helpers\RESTAPIHelper;
use App\Repositories\DropdownRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DropdownController extends Controller
{
    protected $dropdowns;

    public function __construct(DropdownRepository $dropdown)
    {
        $this->dropdowns = $dropdown;

    }

    public function getDropdowns()
    {

/*        $eth = ["Arab","Bangladeshi","African","Far","East","Asian","Hispanic","Indian","Mixed",
            "Pakistani","Persian","Turkish","Caucasian","Other"];
        $res = json_encode(array_sort_recursive($eth));
.        dd($res);*/

  /*      $height = ["4’0”(122cm)","4’1”(124cm)","4’2”(127cm)","4’3”(130cm)","4’4”(132cm)","4’5”(135cm)","4’6”(137cm)","4’7”(140cm)"
            ,"4’8”(142cm)","4’9”(145cm)","4‘10”(147cm)","4’11“(150cm)","5’0”(152cm)","5’1”(155cm)","5’2”(157cm)","5’3”(160cm)"
            ,"5’4”(163cm)","5’5”(165cm)","5’6”(168cm)","5’7”(170cm)","5’8”(173cm)","5’9”(175cm)","5’10”(178cm)","5’11”(180cm)"
            ,"6’0”(183cm)","6’1”(185cm)","6’2”(188cm)","6’3”(190cm)","6’4”(193cm)","6’5”(196cm)","6’6”(198cm)","6’7“(201cm)"
            ,"6’8”(203cm)","6’9”(206cm)","6’10”(208cm)","6’11”(211cm)","7’0”(213cm)","7’1”(216cm)","7’2”(218cm)","7’3”(221cm)"
            ,"7’4”(224cm)","7’5”(226cm)","7’6”(229cm)","7’7”(231cm)","7’8”(234cm)","7’9”(236cm)","7’10”(239cm)","7’11”(241cm)"];*/

/*        $postData['height'] = json_encode($height);
        $this->dropdowns->update($postData,1);*/

        $data = $this->dropdowns->first();

        $res['languages'] = json_decode($data['languages']);
        $res['height'] = json_decode($data['height']);
        $res['ehtnicity'] = json_decode($data['ehtnicity']);
        $res['religion'] = json_decode($data['religion']);
        $res['nationality'] = json_decode($data['nationality']);
        
        return RESTAPIHelper::response($res);


        /*  $lang =  [
              "Pashto","Albanian","Aymara","Azerbaijani","Araona","Arabic","Azeri","Albanian","Afrikaans",
              "Latvian","Bulgarian","Catalan","Czech","Cantonese","Comorian","Croatian","Danish","Estonian"
              ,"Portuguese","English","Spanish","Urdu","French","Faroese","Filipino","Polish","Romani","Ukrainian"
              ,"Finish","German","Greek","Armenian","Hungarian","Russian","Serbian","Spanish","Slovak","Swedish",
              "Mandarin","Turkish","Quechua","Miskito","Hungarian","Nepali","Hindi","Italian","Tamil","Persian",
              "Chinese","Russian","Italian","Serbo-Croatian","Malay","based","Maltese","Māori","Kåfjord,","Nesseby"
              ,"Porsanger","Thai","Bengali","Punjabi","Javanese","Sundanese","Hausa","Fula","Berber","Tuareg","Somali"
              ,"Bosnian","Romanian","Swahili","Swazi","Indonesian","Kurdish","Irish","Hebrew","Korean","Zulu","Sinhala",
              "Pashto,","Balochi","Sindhi","Kashmiri","Tatar"];
          $height = ["4’0”(122cm)","4’1”(124cm)","4’2”(127cm)","4’3”(130cm)","4’4”(132cm)","4’5”(135cm)","4’6”(137cm)","4’7”(140cm)"
              ,"4’8”(142cm)","4’9”(145cm)","4‘10”(147cm)","4’11“(150cm)","5’0”(152cm)","5’1”(155cm)","5’2”(157cm)","5’3”(160cm)"
              ,"5’4”(163cm)","5’5”(165cm)","5’6”(168cm)","5’7”(170cm)","5’8”(173cm)","5’9”(175cm)","5’10”(178cm)","5’11”(180cm)"
              ,"6’0”(183cm)","6’1”(185cm)","6’2”(188cm)","6’3”(190cm)","6’4”(193cm)","6’5”(196cm)","6’6”(198cm)","6’7“(201cm)"
              ,"6’8”(203cm)","6’9”(206cm)","6’10”(208cm)","6’11”(211cm)","7’0”(213cm)","7’1”(216cm)","7’2”(218cm)","7’3”(221cm)"
              ,"7’4”(224cm)","7’5”(226cm)","7’6”(229cm)","7’7”(231cm)","7’8”(234cm)","7’9”(236cm)","7’10”(239cm)","7’11”(241cm)"];
          $eth = ["Arab","Bangladeshi","African","Far","East","Asian","Hispanic","Indian","Mixed",
              "Pakistani","Persian","Turkish","Caucasian","Other"];
         $religion = ['Very Practising','Fairly Practising','Not Practising'];
          $nationality= ["Afghan","Albanian","Algerian","American","Andorran","Angolan","Antiguans","Argentinean","Armenian","Australian","Austrian","Azerbaijani","Bahamian","Bahraini","Bangladeshi","Barbadian","Barbudans","Batswana","Belarusian","Belgian","Belizean","Beninese","Bhutanese","Bolivian","Bosnian","Brazilian","British","Bruneian","Bulgarian","Burkinabe","Burmese","Burundian","Cambodian","Cameroonian","Canadian","Cape","Verdean","Central","African","Chadian","Chilean","Chinese","Colombian","Comoran","Congolese","Costa","Rican","Croatian","Cuban","Cypriot","Czech","Danish","Djibouti","Dominican","Dutch","East","Timorese","Ecuadorean","Egyptian","Emirian","Equatorial","Guinean","Eritrean","Estonian","Ethiopian","Fijian","Filipino","Finnish","French","Gabonese","Gambian","Georgian","German","Ghanaian","Greek","Grenadian","Guatemalan","Guinea-Bissauan","Guinean","Guyanese","Haitian","Herzegovinian","Honduran","Hungarian","I-Kiribati","Icelander","Indian","Indonesian","Iranian","Iraqi","Irish","Israeli","Italian","Ivorian","Jamaican","Japanese","Jordanian","Kazakhstani","Kenyan","Kittian","and","Nevisian","Kuwaiti","Kyrgyz","Laotian","Latvian","Lebanese","Liberian","Libyan","Liechtensteiner","Lithuanian","Luxembourger","Macedonian","Malagasy","Malawian","Malaysian","Maldivian","Malian","Maltese","Marshallese","Mauritanian","Mauritian","Mexican","Micronesian","Moldovan","Monacan","Mongolian","Moroccan","Mosotho","Motswana","Mozambican","Namibian","Nauruan","Nepalese","New","Zealander","Ni-Vanuatu","Nicaraguan","Nigerian","Nigerien","North","Korean","Northern","Irish","Norwegian","Omani","Pakistani","Palauan","Panamanian","Papua","New","Guinean","Paraguayan","Peruvian","Polish","Portuguese","Qatari","Romanian","Russian","Rwandan","Saint","Lucian","Salvadoran","Samoan","San","Marinese","Sao","Tomean","Saudi","Scottish","Senegalese","Serbian","Seychellois","Sierra","Leonean","Singaporean","Slovakian","Slovenian","Solomon","Islander","Somali","South","African","South","Korean","Spanish","Sri","Lankan","Sudanese","Surinamer","Swazi","Swedish","Swiss","Syrian","Taiwanese","Tajik","Tanzanian","Thai","Togolese","Tongan","Trinidadian","or","Tobagonian","Tunisian","Turkish","Tuvaluan","Ugandan","Ukrainian","Uruguayan","Uzbekistani","Venezuelan","Vietnamese","Welsh","Yemenite","Zambian","Zimbabwean"];


        $res['languages'] = json_encode(array_sort_recursive($lang));
        $res['height'] = json_encode(array_sort_recursive($height));
        $res['ehtnicity'] = json_encode(array_sort_recursive($eth));
        $res['religion'] = json_encode(array_sort_recursive($religion));
        $res['nationality'] = json_encode(array_sort_recursive($nationality));

        $this->dropdowns->create($res);*/


    }
}
