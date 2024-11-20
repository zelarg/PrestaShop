import {expect} from 'chai';
import testContext from '@utils/testContext';
import brandsPage from '@pages/BO/catalog/brands';

import {
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

let browserContext: BrowserContext;
let page: Page;

/**
 * Function to delete brand
 * @param brandName {string}
 * @param baseContext {string} String to identify the test
 */
function opsBulkDeleteBrands(brandName: string, baseContext: string = 'commonTests-opsDeleteBrand'): void {
  describe('Delete brand', async () => {
    let numberOfBrands: number = 0;
    let numberOfBrandsToDelete: number = 0;

    before(async function () {
      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);
    });

    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Catalog > Brands & Suppliers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToBrandsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.brandsAndSuppliersLink,
      );
      await brandsPage.closeSfToolBar(page);

      const pageTitle = await brandsPage.getPageTitle(page);
      expect(pageTitle).to.contains(brandsPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      numberOfBrands = await brandsPage.resetAndGetNumberOfLines(page, 'manufacturer');
      expect(numberOfBrands).to.gt(0);
    });

    it(`should filter by brand name '${brandName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkDelete', baseContext);

      await brandsPage.filterBrands(page, 'input', 'name', brandName);

      numberOfBrandsToDelete = await brandsPage.getNumberOfElementInGrid(page, 'manufacturer');
      expect(numberOfBrandsToDelete).to.be.above(0);
    });

    it('should delete brands by Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteFeatures', baseContext);

      const textResult = await brandsPage.deleteWithBulkActions(page, 'manufacturer');
      expect(textResult).to.be.contains(brandsPage.successfulDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      const numberOfBrandsAfterDelete = await brandsPage.resetAndGetNumberOfLines(page, 'manufacturer');
      expect(numberOfBrandsAfterDelete).to.equal(numberOfBrands - numberOfBrandsToDelete);
    });
  });
}

export default opsBulkDeleteBrands;
