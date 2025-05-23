CREATE TABLE `USER`
(
    id       INT AUTO_INCREMENT PRIMARY KEY,
    email    VARCHAR(254) NOT NULL UNIQUE,
    password VARCHAR(64)  NOT NULL,
    role     ENUM('Admin', 'Regular') DEFAULT 'Regular'
);

INSERT INTO `USER` (email, password, role)
VALUES ('123@mail.com', "$2y$12$IVDZrZ24jEO0uDlCUVQTReN7V6yW8pP4A7B1Kmbnn4e9hzAMvvQUK", 'Regular'),
        ('123admin@mail.com', "$2y$12$IVDZrZ24jEO0uDlCUVQTReN7V6yW8pP4A7B1Kmbnn4e9hzAMvvQUK", 'Admin');

CREATE TABLE `MACHINE`
(
    id           VARCHAR(100) PRIMARY KEY,
    display_name VARCHAR(100) NOT NULL,
    icon_name    VARCHAR(100) NOT NULL
);

CREATE TABLE `ITEM`
(
    id            VARCHAR(100) PRIMARY KEY,
    display_name  VARCHAR(100) NOT NULL,
    icon_name     VARCHAR(100) NOT NULL,
    category      VARCHAR(100) NOT NULL,
    display_order int          NOT NULL
);

CREATE TABLE `PRODUCTION PLAN`
(
    id           INT AUTO_INCREMENT PRIMARY KEY,
    created_by   INT          NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    CONSTRAINT fk_created_by FOREIGN KEY (created_by) REFERENCES USER (id)
);

CREATE TABLE `PRODUCTION PLAN CONTENT`
(
    plan_id INT          NOT NULL,
    item_id VARCHAR(100) NOT NULL,
    amount  DOUBLE       NOT NULL,
    PRIMARY KEY (plan_id, item_id),
    CONSTRAINT fk_plan_id FOREIGN KEY (plan_id) REFERENCES `PRODUCTION PLAN` (id),
    CONSTRAINT fk_item_id_content FOREIGN KEY (item_id) REFERENCES ITEM (id)
);

CREATE TABLE `RECIPE`
(
    id           VARCHAR(100) PRIMARY KEY,
    produced_in  VARCHAR(100) NOT NULL,
    display_name VARCHAR(100) NOT NULL
);

CREATE TABLE `RECIPE INPUT`
(
    recipe_id VARCHAR(100) NOT NULL,
    item_id   VARCHAR(100) NOT NULL,
    amount    DOUBLE       NOT NULL,
    PRIMARY KEY (recipe_id, item_id),
    CONSTRAINT fk_recipe_id_input FOREIGN KEY (recipe_id) REFERENCES `RECIPE` (id),
    CONSTRAINT fk_item_id_input FOREIGN KEY (item_id) REFERENCES ITEM (id)
);

CREATE TABLE `RECIPE OUTPUT`
(
    recipe_id          VARCHAR(100) NOT NULL,
    item_id            VARCHAR(100) NOT NULL,
    amount             DOUBLE       NOT NULL,
    is_standard_recipe TINYINT(1)   NOT NULL,
    PRIMARY KEY (recipe_id, item_id),
    CONSTRAINT fk_recipe_id_output FOREIGN KEY (recipe_id) REFERENCES `RECIPE` (id),
    CONSTRAINT fk_item_id_output FOREIGN KEY (item_id) REFERENCES ITEM (id)
);

CREATE TABLE `UTILITY ITEM`
(
    id            INT AUTO_INCREMENT PRIMARY KEY,
    category      VARCHAR(255) NOT NULL,
    native_class  VARCHAR(255),
    item_id       VARCHAR(255),
    display_order int
);

INSERT INTO `UTILITY ITEM` (category, native_class, item_id, display_order)
VALUES ('Native Class', "/Script/CoreUObject.Class'/Script/FactoryGame.FGItemDescriptor'", NULL, NULL),
       ('Native Class', "/Script/CoreUObject.Class'/Script/FactoryGame.FGResourceDescriptor'", NULL, NULL),
       ('Native Class', "/Script/CoreUObject.Class'/Script/FactoryGame.FGItemDescriptorBiomass'", NULL, NULL),
       ('Native Class', "/Script/CoreUObject.Class'/Script/FactoryGame.FGItemDescriptorNuclearFuel'", NULL, NULL),
       ('Native Class', "/Script/CoreUObject.Class'/Script/FactoryGame.FGPowerShardDescriptor'", NULL, NULL),
       ('Native Class', "/Script/CoreUObject.Class'/Script/FactoryGame.FGAmmoTypeProjectile'", NULL, NULL),
       ('Native Class', "/Script/CoreUObject.Class'/Script/FactoryGame.FGAmmoTypeSpreadshot'", NULL, NULL),
       ('Native Class', "/Script/CoreUObject.Class'/Script/FactoryGame.FGAmmoTypeInstantHit'", NULL, NULL),
       ('Native Class', "/Script/CoreUObject.Class'/Script/FactoryGame.FGItemDescriptorPowerBoosterFuel'", NULL, NULL),
       ('Ficsmas', NULL, 'Desc_Gift_C', NULL),
       ('Ficsmas', NULL, 'Desc_CandyCane_C', NULL),
       ('Ficsmas', NULL, 'Desc_Snow_C', NULL),
       ('Ficsmas', NULL, 'Desc_XmasBallCluster_C', NULL),
       ('Ficsmas', NULL, 'Desc_XmasStar_C', NULL),
       ('Ficsmas', NULL, 'Desc_XmasWreath_C', NULL),
       ('Ficsmas', NULL, 'Desc_XmasBow_C', NULL),
       ('Ficsmas', NULL, 'Desc_XmasBall1_C', NULL),
       ('Ficsmas', NULL, 'Desc_XmasBall2_C', NULL),
       ('Ficsmas', NULL, 'Desc_XmasBall3_C', NULL),
       ('Ficsmas', NULL, 'Desc_XmasBall4_C', NULL),
       ('Ficsmas', NULL, 'Desc_XmasBranch_C', NULL),
       ('Raw Resources', NULL, 'Desc_OreIron_C', 1),
       ('Raw Resources', NULL, 'Desc_OreCopper_C', 2),
       ('Raw Resources', NULL, 'Desc_Stone_C', 3),
       ('Raw Resources', NULL, 'Desc_Coal_C', 4),
       ('Raw Resources', NULL, 'Desc_Water_C', 5),
       ('Raw Resources', NULL, 'Desc_PackagedWater_C', 6),
       ('Raw Resources', NULL, 'Desc_LiquidOil_C', 7),
       ('Raw Resources', NULL, 'Desc_PackagedOil_C', 8),
       ('Raw Resources', NULL, 'Desc_OreGold_C', 9),
       ('Raw Resources', NULL, 'Desc_OreBauxite_C', 10),
       ('Raw Resources', NULL, 'Desc_RawQuartz_C', 11),
       ('Raw Resources', NULL, 'Desc_Sulfur_C', 12),
       ('Raw Resources', NULL, 'Desc_OreUranium_C', 13),
       ('Raw Resources', NULL, 'Desc_NitrogenGas_C', 14),
       ('Raw Resources', NULL, 'Desc_PackagedNitrogenGas_C', 15),
       ('Raw Resources', NULL, 'Desc_SAM_C', 16),
       ('Collectable', NULL, 'Desc_Leaves_C', 1),
       ('Collectable', NULL, 'Desc_Wood_C', 2),
       ('Collectable', NULL, 'Desc_Mycelia_C', 3),
       ('Collectable', NULL, 'Desc_HogParts_C', 4),
       ('Collectable', NULL, 'Desc_SpitterParts_C', 5),
       ('Collectable', NULL, 'Desc_HatcherParts_C', 6),
       ('Collectable', NULL, 'Desc_StingerParts_C', 7),
       ('Collectable', NULL, 'Desc_Crystal_C', 8),
       ('Collectable', NULL, 'Desc_Crystal_mk2_C', 9),
       ('Collectable', NULL, 'Desc_Crystal_mk3_C', 10),
       ('Collectable', NULL, 'Desc_WAT1_C', 11),
       ('Collectable', NULL, 'Desc_WAT2_C', 12),
       ('Tier 0', NULL, 'Desc_IronIngot_C', 1),
       ('Tier 0', NULL, 'Desc_IronPlate_C', 2),
       ('Tier 0', NULL, 'Desc_IronRod_C', 3),
       ('Tier 0', NULL, 'Desc_CopperIngot_C', 4),
       ('Tier 0', NULL, 'Desc_Wire_C', 5),
       ('Tier 0', NULL, 'Desc_Cable_C', 6),
       ('Tier 0', NULL, 'Desc_Cement_C', 7),
       ('Tier 0', NULL, 'Desc_IronScrew_C', 8),
       ('Tier 0', NULL, 'Desc_IronPlateReinforced_C', 9),
       ('Tier 0', NULL, 'Desc_GenericBiomass_C', 10),
       ('Tier 2', NULL, 'Desc_CopperSheet_C', 1),
       ('Tier 2', NULL, 'Desc_Rotor_C', 2),
       ('Tier 2', NULL, 'Desc_ModularFrame_C', 3),
       ('Tier 2', NULL, 'Desc_SpaceElevatorPart_1_C', 4),
       ('Tier 2', NULL, 'Desc_Biofuel_C', 5),
       ('Tier 3', NULL, 'Desc_SteelIngot_C', 1),
       ('Tier 3', NULL, 'Desc_SteelPlate_C', 2),
       ('Tier 3', NULL, 'Desc_SteelPipe_C', 3),
       ('Tier 3', NULL, 'Desc_SpaceElevatorPart_2_C', 4),
       ('Tier 4', NULL, 'Desc_SteelPlateReinforced_C', 1),
       ('Tier 4', NULL, 'Desc_Stator_C', 2),
       ('Tier 4', NULL, 'Desc_Motor_C', 3),
       ('Tier 4', NULL, 'Desc_SpaceElevatorPart_3_C', 4),
       ('Tier 5', NULL, 'Desc_Plastic_C', 1),
       ('Tier 5', NULL, 'Desc_Rubber_C', 2),
       ('Tier 5', NULL, 'Desc_PolymerResin_C', 3),
       ('Tier 5', NULL, 'Desc_PetroleumCoke_C', 4),
       ('Tier 5', NULL, 'Desc_CircuitBoard_C', 5),
       ('Tier 5', NULL, 'Desc_LiquidFuel_C', 6),
       ('Tier 5', NULL, 'Desc_Fuel_C', 7),
       ('Tier 5', NULL, 'Desc_HeavyOilResidue_C', 8),
       ('Tier 5', NULL, 'Desc_PackagedOilResidue_C', 9),
       ('Tier 5', NULL, 'Desc_LiquidBiofuel_C', 10),
       ('Tier 5', NULL, 'Desc_PackagedBiofuel_C', 11),
       ('Tier 5', NULL, 'Desc_FluidCanister_C', 12),
       ('Tier 6', NULL, 'Desc_Computer_C', 1),
       ('Tier 6', NULL, 'Desc_ModularFrameHeavy_C', 2),
       ('Tier 6', NULL, 'Desc_SpaceElevatorPart_4_C', 3),
       ('Tier 6', NULL, 'Desc_SpaceElevatorPart_5_C', 4),
       ('Tier 7', NULL, 'Desc_AluminaSolution_C', 1),
       ('Tier 7', NULL, 'Desc_PackagedAlumina_C', 2),
       ('Tier 7', NULL, 'Desc_AluminumScrap_C', 3),
       ('Tier 7', NULL, 'Desc_AluminumIngot_C', 4),
       ('Tier 7', NULL, 'Desc_AluminumPlate_C', 5),
       ('Tier 7', NULL, 'Desc_AluminumCasing_C', 6),
       ('Tier 7', NULL, 'Desc_ModularFrameLightweight_C', 7),
       ('Tier 7', NULL, 'Desc_SulfuricAcid_C', 8),
       ('Tier 7', NULL, 'Desc_PackagedSulfuricAcid_C', 9),
       ('Tier 7', NULL, 'Desc_Battery_C', 10),
       ('Tier 7', NULL, 'Desc_ComputerSuper_C', 11),
       ('Tier 7', NULL, 'Desc_SpaceElevatorPart_7_C', 12),
       ('Tier 8', NULL, 'Desc_UraniumCell_C', 1),
       ('Tier 8', NULL, 'Desc_ElectromagneticControlRod_C', 2),
       ('Tier 8', NULL, 'Desc_NuclearFuelRod_C', 3),
       ('Tier 8', NULL, 'Desc_NuclearWaste_C', 4),
       ('Tier 8', NULL, 'Desc_SpaceElevatorPart_6_C', 5),
       ('Tier 8', NULL, 'Desc_GasTank_C', 6),
       ('Tier 8', NULL, 'Desc_AluminumPlateReinforced_C', 7),
       ('Tier 8', NULL, 'Desc_CoolingSystem_C', 8),
       ('Tier 8', NULL, 'Desc_ModularFrameFused_C', 9),
       ('Tier 8', NULL, 'Desc_MotorLightweight_C', 10),
       ('Tier 8', NULL, 'Desc_SpaceElevatorPart_8_C', 11),
       ('Tier 8', NULL, 'Desc_NitricAcid_C', 12),
       ('Tier 8', NULL, 'Desc_PackagedNitricAcid_C', 13),
       ('Tier 8', NULL, 'Desc_NonFissibleUranium_C', 14),
       ('Tier 8', NULL, 'Desc_PlutoniumPellet_C', 15),
       ('Tier 8', NULL, 'Desc_PlutoniumCell_C', 16),
       ('Tier 8', NULL, 'Desc_PlutoniumFuelRod_C', 17),
       ('Tier 8', NULL, 'Desc_PlutoniumWaste_C', 18),
       ('Tier 8', NULL, 'Desc_CopperDust_C', 19),
       ('Tier 8', NULL, 'Desc_PressureConversionCube_C', 20),
       ('Tier 8', NULL, 'Desc_SpaceElevatorPart_9_C', 21),
       ('Tier 9', NULL, 'Desc_Diamond_C', 1),
       ('Tier 9', NULL, 'Desc_TimeCrystal_C', 2),
       ('Tier 9', NULL, 'Desc_FicsiteIngot_C', 3),
       ('Tier 9', NULL, 'Desc_FicsiteMesh_C', 4),
       ('Tier 9', NULL, 'Desc_SpaceElevatorPart_10_C', 5),
       ('Tier 9', NULL, 'Desc_QuantumEnergy_C', 6),
       ('Tier 9', NULL, 'Desc_DarkEnergy_C', 7),
       ('Tier 9', NULL, 'Desc_DarkMatter_C', 8),
       ('Tier 9', NULL, 'Desc_QuantumOscillator_C', 9),
       ('Tier 9', NULL, 'Desc_TemporalProcessor_C', 10),
       ('Tier 9', NULL, 'Desc_SpaceElevatorPart_12_C', 11),
       ('Tier 9', NULL, 'Desc_SingularityCell_C', 12),
       ('Tier 9', NULL, 'Desc_SpaceElevatorPart_11_C', 13),
       ('Tier 9', NULL, 'Desc_Ficsonium_C', 14),
       ('Tier 9', NULL, 'Desc_FicsoniumFuelRod_C', 15),
       ('MAM', NULL, 'Desc_CrystalShard_C', 1),
       ('MAM', NULL, 'Desc_AlienProtein_C', 2),
       ('MAM', NULL, 'Desc_AlienDNACapsule_C', 3),
       ('MAM', NULL, 'Desc_Fabric_C', 4),
       ('MAM', NULL, 'Desc_GoldIngot_C', 5),
       ('MAM', NULL, 'Desc_HighSpeedWire_C', 6),
       ('MAM', NULL, 'Desc_CircuitBoardHighSpeed_C', 7),
       ('MAM', NULL, 'Desc_HighSpeedConnector_C', 8),
       ('MAM', NULL, 'Desc_QuartzCrystal_C', 9),
       ('MAM', NULL, 'Desc_Silica_C', 10),
       ('MAM', NULL, 'Desc_CrystalOscillator_C', 11),
       ('MAM', NULL, 'Desc_DissolvedSilica_C', 12),
       ('MAM', NULL, 'Desc_Gunpowder_C', 13),
       ('MAM', NULL, 'Desc_CompactedCoal_C', 14),
       ('MAM', NULL, 'Desc_LiquidTurboFuel_C', 15),
       ('MAM', NULL, 'Desc_TurboFuel_C', 16),
       ('MAM', NULL, 'Desc_GunpowderMK2_C', 17),
       ('MAM', NULL, 'Desc_RocketFuel_C', 18),
       ('MAM', NULL, 'Desc_PackagedRocketFuel_C', 19),
       ('MAM', NULL, 'Desc_IonizedFuel_C', 20),
       ('MAM', NULL, 'Desc_PackagedIonizedFuel_C', 21),
       ('MAM', NULL, 'Desc_SAMIngot_C', 22),
       ('MAM', NULL, 'Desc_SAMFluctuator_C', 23),
       ('MAM', NULL, 'Desc_AlienPowerFuel_C', 24),
       ('Equipment', NULL, 'Desc_Filter_C', 1),
       ('Equipment', NULL, 'Desc_HazmatFilter_C', 2),
       ('Equipment', NULL, 'Desc_SpikedRebar_C', 3),
       ('Equipment', NULL, 'Desc_Rebar_Stunshot_C', 4),
       ('Equipment', NULL, 'Desc_Rebar_Spreadshot_C', 5),
       ('Equipment', NULL, 'Desc_Rebar_Explosive_C', 6),
       ('Equipment', NULL, 'Desc_NobeliskExplosive_C', 7),
       ('Equipment', NULL, 'Desc_NobeliskGas_C', 8),
       ('Equipment', NULL, 'Desc_NobeliskShockwave_C', 9),
       ('Equipment', NULL, 'Desc_NobeliskCluster_C', 10),
       ('Equipment', NULL, 'Desc_NobeliskNuke_C', 11),
       ('Equipment', NULL, 'Desc_CartridgeStandard_C', 12),
       ('Equipment', NULL, 'Desc_CartridgeSmartProjectile_C', 13),
       ('Equipment', NULL, 'Desc_CartridgeChaos_C', 14);

CREATE TABLE `UTILITY MACHINE`
(
    id           INT AUTO_INCREMENT PRIMARY KEY,
    category     VARCHAR(255) NOT NULL,
    native_class VARCHAR(255)
);

INSERT INTO `UTILITY MACHINE` (category, native_class)
VALUES ('Native Class', "/Script/CoreUObject.Class'/Script/FactoryGame.FGBuildableManufacturer'"),
       ('Native Class', "/Script/CoreUObject.Class'/Script/FactoryGame.FGBuildableManufacturerVariablePower'"),
       ('Native Class', "/Script/CoreUObject.Class'/Script/FactoryGame.FGBuildableResourceExtractor'"),
       ('Native Class', "/Script/CoreUObject.Class'/Script/FactoryGame.FGBuildableWaterPump'"),
       ('Native Class', "/Script/CoreUObject.Class'/Script/FactoryGame.FGBuildableFrackingExtractor'");

CREATE TABLE `UTILITY RECIPE`
(
    id                   INT AUTO_INCREMENT PRIMARY KEY,
    category             VARCHAR(255) NOT NULL,
    recipe_id            VARCHAR(255),
    item_id              VARCHAR(255),
    machine_id           VARCHAR(255),
    display_name         VARCHAR(255),
    amount               INT,
    is_standard_recipe   TINYINT(1),
    alternative_output_1 VARCHAR(255),
    alternative_output_2 VARCHAR(255),
    standard_output      VARCHAR(255)
);

INSERT INTO `UTILITY RECIPE` (category, recipe_id, machine_id, display_name, amount, is_standard_recipe,
                              alternative_output_1, alternative_output_2, standard_output)
VALUES ('Ficsmas', 'Recipe_XmasBall1_C', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
       ('Ficsmas', 'Recipe_XmasBall2_C', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
       ('Ficsmas', 'Recipe_XmasBall3_C', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
       ('Ficsmas', 'Recipe_XmasBall4_C', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
       ('Ficsmas', 'Recipe_XmasBallCluster_C', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
       ('Ficsmas', 'Recipe_XmasBow_C', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
       ('Ficsmas', 'Recipe_XmasBranch_C', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
       ('Ficsmas', 'Recipe_XmasStar_C', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
       ('Ficsmas', 'Recipe_XmasWreath_C', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
       ('Ficsmas', 'Recipe_CandyCane_C', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
       ('Ficsmas', 'Recipe_Snow_C', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
       ('Ficsmas', 'Recipe_Snowball_C', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
       ('Ficsmas', 'Recipe_Fireworks_01_C', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
       ('Ficsmas', 'Recipe_Fireworks_02_C', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
       ('Ficsmas', 'Recipe_Fireworks_03_C', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
       ('Resource Recipe', 'Recipe_IronOre_C', 'Build_MinerMk1_C', 'Iron Ore', NULL, NULL, NULL, NULL, NULL),
       ('Resource Recipe', 'Recipe_CopperOre_C', 'Build_MinerMk1_C', 'Copper Ore', NULL, NULL, NULL, NULL, NULL),
       ('Resource Recipe', 'Recipe_LimestoneOre_C', 'Build_MinerMk1_C', 'Limestone', NULL, NULL, NULL, NULL, NULL),
       ('Resource Recipe', 'Recipe_Coal_C', 'Build_MinerMk1_C', 'Coal', NULL, NULL, NULL, NULL, NULL),
       ('Resource Recipe', 'Recipe_Water_C', 'Build_WaterPump_C', 'Water', NULL, NULL, NULL, NULL, NULL),
       ('Resource Recipe', 'Recipe_LiquidOil_C', 'Build_OilPump_C', 'Crude Oil', NULL, NULL, NULL, NULL, NULL),
       ('Resource Recipe', 'Recipe_CateriumOre_C', 'Build_MinerMk1_C', 'Caterium Ore', NULL, NULL, NULL, NULL, NULL),
       ('Resource Recipe', 'Recipe_Bauxite_C', 'Build_MinerMk1_C', 'Bauxite', NULL, NULL, NULL, NULL, NULL),
       ('Resource Recipe', 'Recipe_RawQuartz_C', 'Build_MinerMk1_C', 'Raw Quartz', NULL, NULL, NULL, NULL, NULL),
       ('Resource Recipe', 'Recipe_Sulfur_C', 'Build_MinerMk1_C', 'Sulphur', NULL, NULL, NULL, NULL, NULL),
       ('Resource Recipe', 'Recipe_Uranium_C', 'Build_MinerMk1_C', 'Uranium', NULL, NULL, NULL, NULL, NULL),
       ('Resource Recipe', 'Recipe_NitrogenGas_C', 'Build_FrackingExtractor_C', 'Nitrogen Gas', NULL, NULL, NULL, NULL,
        NULL),
       ('Resource Recipe', 'Recipe_SAM_C', 'Build_MinerMk1_C', 'SAM', NULL, NULL, NULL, NULL, NULL),
       ('Resource Recipe Output', 'Recipe_IronOre_C', NULL, NULL, 120, 1, NULL, NULL, 'Desc_OreIron_C'),
       ('Resource Recipe Output', 'Recipe_CopperOre_C', NULL, NULL, 120, 1, NULL, NULL, 'Desc_OreCopper_C'),
       ('Resource Recipe Output', 'Recipe_LimestoneOre_C', NULL, NULL, 120, 1, NULL, NULL, 'Desc_Stone_C'),
       ('Resource Recipe Output', 'Recipe_Coal_C', NULL, NULL, 120, 1, NULL, NULL, 'Desc_Coal_C'),
       ('Resource Recipe Output', 'Recipe_Water_C', NULL, NULL, 120, 1, NULL, NULL, 'Desc_Water_C'),
       ('Resource Recipe Output', 'Recipe_LiquidOil_C', NULL, NULL, 120, 1, NULL, NULL, 'Desc_LiquidOil_C'),
       ('Resource Recipe Output', 'Recipe_CateriumOre_C', NULL, NULL, 120, 1, NULL, NULL, 'Desc_OreGold_C'),
       ('Resource Recipe Output', 'Recipe_Bauxite_C', NULL, NULL, 120, 1, NULL, NULL, 'Desc_OreBauxite_C'),
       ('Resource Recipe Output', 'Recipe_RawQuartz_C', NULL, NULL, 120, 1, NULL, NULL, 'Desc_RawQuartz_C'),
       ('Resource Recipe Output', 'Recipe_Sulfur_C', NULL, NULL, 120, 1, NULL, NULL, 'Desc_Sulfur_C'),
       ('Resource Recipe Output', 'Recipe_Uranium_C', NULL, NULL, 120, 1, NULL, NULL, 'Desc_OreUranium_C'),
       ('Resource Recipe Output', 'Recipe_NitrogenGas_C', NULL, NULL, 60, 1, NULL, NULL, 'Desc_NitrogenGas_C'),
       ('Resource Recipe Output', 'Recipe_SAM_C', NULL, NULL, 120, 1, NULL, NULL, 'Desc_SAM_C'),
       ('Alternative Recipe Output', 'Recipe_ResidualPlastic_C', NULL, NULL, NULL, NULL, 'Desc_Plastic_C', NULL, NULL),
       ('Alternative Recipe Output', 'Recipe_AluminumScrap_C', NULL, NULL, NULL, NULL, 'Desc_Water_C', NULL, NULL),
       ('Alternative Recipe Output', 'Recipe_Battery_C', NULL, NULL, NULL, NULL, 'Desc_Water_C', NULL, NULL),
       ('Alternative Recipe Output', 'Recipe_NonFissileUranium_C', NULL, NULL, NULL, NULL, 'Desc_Water_C', NULL, NULL),
       ('Alternative Recipe Output', 'Recipe_UnpackageWater_C', NULL, NULL, NULL, NULL, 'Desc_Water_C',
        'Desc_FluidCanister_C', NULL),
       ('Alternative Recipe Output', 'Recipe_Protein_Crab_C', NULL, NULL, NULL, NULL, 'Desc_AlienProtein_C', NULL,
        NULL),
       ('Alternative Recipe Output', 'Recipe_Protein_Spitter_C', NULL, NULL, NULL, NULL, 'Desc_AlienProtein_C', NULL,
        NULL),
       ('Alternative Recipe Output', 'Recipe_Protein_Stinger_C', NULL, NULL, NULL, NULL, 'Desc_AlienProtein_C', NULL,
        NULL),
       ('Alternative Recipe Output', 'Recipe_PureAluminumIngot_C', NULL, NULL, NULL, NULL, 'Desc_AluminumIngot_C', NULL,
        NULL),
       ('Alternative Recipe Output', 'Recipe_UnpackageAlumina_C', NULL, NULL, NULL, NULL, 'Desc_AluminaSolution_C',
        'Desc_FluidCanister_C', NULL),
       ('Alternative Recipe Output', 'Recipe_CartridgeChaos_Packaged_C', NULL, NULL, NULL, NULL,
        'Desc_CartridgeChaos_C', NULL, NULL),
       ('Alternative Recipe Output', 'Recipe_IonizedFuel_C', NULL, NULL, NULL, NULL, 'Desc_CompactedCoal_C', NULL,
        NULL),
       ('Alternative Recipe Output', 'Recipe_RocketFuel_C', NULL, NULL, NULL, NULL, 'Desc_CompactedCoal_C', NULL, NULL),
       ('Alternative Recipe Output', 'Recipe_PowerCrystalShard_2_C', NULL, NULL, NULL, NULL, 'Desc_CrystalShard_C',
        NULL, NULL),
       ('Alternative Recipe Output', 'Recipe_PowerCrystalShard_3_C', NULL, NULL, NULL, NULL, 'Desc_CrystalShard_C',
        NULL, NULL),
       ('Alternative Recipe Output', 'Recipe_SyntheticPowerShard_C', NULL, NULL, NULL, NULL, 'Desc_CrystalShard_C',
        'Desc_DarkEnergy_C', NULL),
       ('Alternative Recipe Output', 'Recipe_AlienPowerFuel_C', NULL, NULL, NULL, NULL, 'Desc_DarkEnergy_C', NULL,
        NULL),
       ('Alternative Recipe Output', 'Recipe_FicsoniumFuelRod_C', NULL, NULL, NULL, NULL, 'Desc_DarkEnergy_C', NULL,
        NULL),
       ('Alternative Recipe Output', 'Recipe_SpaceElevatorPart_12_C', NULL, NULL, NULL, NULL, 'Desc_DarkEnergy_C', NULL,
        NULL),
       ('Alternative Recipe Output', 'Recipe_SuperpositionOscillator_C', NULL, NULL, NULL, NULL, 'Desc_DarkEnergy_C',
        NULL, NULL),
       ('Alternative Recipe Output', 'Recipe_TemporalProcessor_C', NULL, NULL, NULL, NULL, 'Desc_DarkEnergy_C', NULL,
        NULL),
       ('Alternative Recipe Output', 'Recipe_UnpackageBioFuel_C', NULL, NULL, NULL, NULL, 'Desc_FluidCanister_C',
        'Desc_LiquidBiofuel_C', NULL),
       ('Alternative Recipe Output', 'Recipe_UnpackageFuel_C', NULL, NULL, NULL, NULL, 'Desc_FluidCanister_C',
        'Desc_LiquidFuel_C', NULL),
       ('Alternative Recipe Output', 'Recipe_UnpackageOil_C', NULL, NULL, NULL, NULL, 'Desc_FluidCanister_C',
        'Desc_LiquidOil_C', NULL),
       ('Alternative Recipe Output', 'Recipe_UnpackageOilResidue_C', NULL, NULL, NULL, NULL, 'Desc_FluidCanister_C',
        'Desc_HeavyOilResidue_C', NULL),
       ('Alternative Recipe Output', 'Recipe_UnpackageSulfuricAcid_C', NULL, NULL, NULL, NULL, 'Desc_FluidCanister_C',
        'Desc_SulfuricAcid_C', NULL),
       ('Alternative Recipe Output', 'Recipe_UnpackageTurboFuel_C', NULL, NULL, NULL, NULL, 'Desc_FluidCanister_C',
        NULL, NULL),
       ('Alternative Recipe Output', 'Recipe_UnpackageIonizedFuel_C', NULL, NULL, NULL, NULL, 'Desc_GasTank_C',
        'Desc_IonizedFuel_C', NULL),
       ('Alternative Recipe Output', 'Recipe_UnpackageNitricAcid_C', NULL, NULL, NULL, NULL, 'Desc_GasTank_C',
        'Desc_NitricAcid_C', NULL),
       ('Alternative Recipe Output', 'Recipe_UnpackageNitrogen_C', NULL, NULL, NULL, NULL, 'Desc_GasTank_C',
        'Desc_NitrogenGas_C', NULL),
       ('Alternative Recipe Output', 'Recipe_UnpackageRocketFuel_C', NULL, NULL, NULL, NULL, 'Desc_GasTank_C',
        'Desc_RocketFuel_C', NULL),
       ('Alternative Recipe Output', 'Recipe_Biomass_Leaves_C', NULL, NULL, NULL, NULL, 'Desc_GenericBiomass_C', NULL,
        NULL),
       ('Alternative Recipe Output', 'Recipe_Biomass_Mycelia_C', NULL, NULL, NULL, NULL, 'Desc_GenericBiomass_C', NULL,
        NULL),
       ('Alternative Recipe Output', 'Recipe_Biomass_Wood_C', NULL, NULL, NULL, NULL, 'Desc_GenericBiomass_C', NULL,
        NULL),
       ('Alternative Recipe Output', 'Recipe_Rubber_C', NULL, NULL, NULL, NULL, 'Desc_HeavyOilResidue_C', NULL, NULL),
       ('Alternative Recipe Output', 'Recipe_ResidualFuel_C', NULL, NULL, NULL, NULL, 'Desc_LiquidFuel_C', NULL, NULL),
       ('Alternative Recipe Output', 'Recipe_ResidualRubber_C', NULL, NULL, NULL, NULL, 'Desc_Rubber_C', NULL, NULL),
       ('Alternative Recipe Output', 'Recipe_AluminaSolution_C', NULL, NULL, NULL, NULL, 'Desc_Silica_C', NULL, NULL),
       ('Alternative Recipe Output', 'Recipe_UraniumCell_C', NULL, NULL, NULL, NULL, 'Desc_SulfuricAcid_C', NULL, NULL),
       ('Alternative Recipe Output', 'Recipe_UnpackageTurboFuel_C', NULL, NULL, NULL, NULL, 'Desc_LiquidTurboFuel_C',
        NULL, NULL),
       ('Standard Recipe Output', 'Recipe_Alternate_EnrichedCoal_C', NULL, NULL, NULL, NULL, NULL, NULL,
        'Desc_CompactedCoal_C'),
       ('Standard Recipe Output', 'Recipe_DarkEnergy_C', NULL, NULL, NULL, NULL, NULL, NULL, 'Desc_DarkEnergy_C'),
       ('Standard Recipe Output', 'Recipe_Alternate_Quartz_Purified_C', NULL, NULL, NULL, NULL, NULL, NULL,
        'Desc_DissolvedSilica_C'),
       ('Standard Recipe Output', 'Recipe_FicsiteIngot_Iron_C', NULL, NULL, NULL, NULL, NULL, NULL,
        'Desc_FicsiteIngot_C'),
       ('Standard Recipe Output', 'Recipe_QuantumEnergy_C', NULL, NULL, NULL, NULL, NULL, NULL, 'Desc_QuantumEnergy_C'),
       ('Standard Recipe Output', 'Recipe_TimeCrystal_C', NULL, NULL, NULL, NULL, NULL, NULL, 'Desc_TimeCrystal_C'),
       ('Standard Recipe Output', 'Recipe_Alternate_Turbofuel_C', NULL, NULL, NULL, NULL, NULL, NULL,
        'Desc_LiquidTurboFuel_C');
