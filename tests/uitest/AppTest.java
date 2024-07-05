public void testLoginWithValidCredentials() {
    driver.get(url);

    // Log the current URL for debugging
    System.out.println("Current URL before login: " + driver.getCurrentUrl());
    System.out.println("Page title: " + driver.getTitle());
    System.out.println("Page source: " + driver.getPageSource());

    try {
        wait.until(ExpectedConditions.presenceOfElementLocated(By.name("cust_username")));

        // Log presence of form elements
        System.out.println("Form and input elements are present.");

        driver.findElement(By.name("cust_username")).sendKeys(validUsername);
        driver.findElement(By.name("cust_pass")).sendKeys(validPassword);
        driver.findElement(By.id("submit")).click();

        // Log presence of success popup
        System.out.println("Login form submitted.");

        // Navigate directly to index.php
        driver.get("https://whattheduck.ddns.net/index.php");
        System.out.println("Navigated to index.php.");

        // Wait for the username to appear in the navbar
        wait.until(ExpectedConditions.presenceOfElementLocated(By.xpath("//a[contains(text(),'" + validUsername + "')]")));
        System.out.println("Username found in navbar.");

        // Verify that the username is displayed in the navbar
        WebElement usernameLink = driver.findElement(By.xpath("//a[contains(text(),'" + validUsername + "')]"));
        assertTrue(usernameLink.isDisplayed());
        System.out.println("Username is displayed in the navbar: " + usernameLink.getText());

    } catch (Exception e) {
        System.out.println("Exception: " + e.getMessage());
        System.out.println("Current URL: " + driver.getCurrentUrl());
        System.out.println("Page source: " + driver.getPageSource());
        throw e;
    }
}
