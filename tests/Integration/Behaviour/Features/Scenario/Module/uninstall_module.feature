# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s module --tags uninstall-module
@reset-test-modules-before-feature
@restore-all-tables-before-feature
@clear-cache-before-feature
@reset-test-modules-after-feature
@uninstall-module
Feature: Module
  PrestaShop allows BO users to manage modules
  As a BO user
  I must be able to uninstall modules

  Scenario: Uninstall modules
    Given module ps_featuredproducts has following infos:
      | technical_name    | ps_featuredproducts |
      | installed_version | 1.0.0               |
      | module_version    | 1.0.0               |
      | enabled           | true                |
      | installed         | true                |
    Given module ps_emailsubscription has following infos:
      | technical_name    | ps_emailsubscription |
      | installed_version | 1.0.0                |
      | module_version    | 1.0.0                |
      | enabled           | true                 |
      | installed         | true                 |
    When I bulk uninstall modules: "ps_featuredproducts,ps_emailsubscription" with deleteFile false
    Then module ps_featuredproducts has following infos:
      | technical_name    | ps_featuredproducts |
      | installed_version |                     |
      | module_version    | 1.0.0               |
      | enabled           | false               |
      | installed         | false               |
    And module ps_emailsubscription has following infos:
      | technical_name    | ps_emailsubscription |
      | installed_version |                      |
      | module_version    | 1.0.0                |
      | enabled           | false                |
      | installed         | false                |

  Scenario: Uninstall module
    Given module bankwire has following infos:
      | technical_name    | bankwire |
      | installed_version | 2.0.0    |
      | module_version    | 2.0.0    |
      | enabled           | true     |
      | installed         | true     |
    When I uninstall module "bankwire" with deleteFile true
    # Since the module is completely removed and is not present it cannot be found anymore
    And module bankwire has following infos:
      | installed | false |
    Then I should have an exception that module is not found

  Scenario: Uninstall module that is not installed
    Given module ps_featuredproducts has following infos:
      | technical_name    | ps_featuredproducts |
      | installed_version |                     |
      | module_version    | 1.0.0               |
      | enabled           | false               |
      | installed         | false               |
    When I uninstall module "ps_featuredproducts" with deleteFile false
    Then I should have an exception that module is not installed
