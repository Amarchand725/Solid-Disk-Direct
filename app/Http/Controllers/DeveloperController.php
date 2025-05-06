<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeveloperController extends Controller
{
    public function storeCategories(){
        $parentCats = [
            "Storage Devices",
            "Memory",
            "Network & Accessories",
            "PC & Servers",
            "Softwares",
            "Motherboards",
            "Printers & Scanners",
            "Power Equipment",
            "Processors",
            "Video Components",
            "Audio Components",
            "Input Devices",
            "Cameras",
            "Cables",
            "System Cooling Parts",
            "Monitors",
            "Computer Accessories",
        ];

        $storageDeviceCategories = [
            "Hard Drives",
            "Storage Accessories",
            "Hard Drive Enclosure",
            "Solid State Drives",
            "Storage Array",
            "Controllers",
            "Tape Drives",
            "Tape Media",
            "Tape Libraries & Autoloaders",
            "USB Flash Drives",
            "Memory Cards",
            "I/O Cards/Panel",
            "Floppy Drives",
            "Network Storage Devices",
            "Host Bus Adapter",
            "Host Channel Adapter",
            "Optical Drives & Burners",
            "Optical Media"
          ];

          $hardDrivesSubCategories = [
            "Server Hard Drives",
            "Desktop Hard Drives",
            "Laptop Hard Drives",
            "Printer Hard Drives",
            "External Hard Drives"
          ];

          $storageAccessoriesSubCategories = [
            "Tray/Caddy",
            "Other Storage Accessories"
          ];

          $controllersSubCategories = [
            "SATA/SAS Controllers",
            "SCSI Controllers",
            "Fibre Channel Controllers",
            "Storage Controllers",
            "Raid Controllers"
          ];
          
          $opticalDrivesAndBurnersSubCategories = [
            "CD & DVD Burners",
            "CD Drives",
            "DVD Drives",
            "Blu-ray Drives",
            "CD DVD & Blu-ray Accessories",
            "Blu-ray Burners",
            "External CD DVD & Blu-ray Drives"
          ];

          $opticalMediaSubCategories = [
            "CD Disk",
            "DVD Disk",
            "Blu-ray Disk"
          ];          
          
          //
          $memoryCategories = [
            "Server Memory",
            "Desktop Memory",
            "Laptop Memory",
            "Printer Memory",
            "Network Memory",
            "Gaming Memory",
            "Cache Memory",
            "Flash Memory",
            "Memory Boards"
          ];

          $networkAndAccessoriesCategories = [
            "Wireless Networking",
            "Switches",
            "Routers",
            "VoIP Gateways",
            "Media Converter",
            "Network Adapters",
            "Network Accessories",
            "Transceivers",
            "Switch Module",
            "Modems",
            "Powerline Network Adapters",
            "Power Over Ethernet Adapters",
            "Network Security & Firewall Devices",
            "IP Phones",
            "Print Servers",
            "Router/Switch Chassis",
            "Terminal Servers",
            "Networking Devices",
            "PABX System"
          ];
          $wirelessNetworkingSubcategories = [
            "Wireless Access Points",
            "Wireless Routers",
            "Antennas",
            "Wireless LAN Controller"
          ];
          
          
          
    }
}
