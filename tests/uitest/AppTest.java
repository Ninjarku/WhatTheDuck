import org.junit.After;
import org.junit.Before;
import org.junit.Test;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.htmlunit.HtmlUnitDriver;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import static org.junit.Assert.assertTrue;

public class AppTest {

    private WebDriver driver;
    private WebDriverWait wait;
    private String url = "https://whattheduck.ddns.net/Login.php";  
    private String validUsername = System.getenv("TEST_USERNAME");
    private String validPassword = System.getenv("TEST_PASSWORD");
    private String invalidUsername = "invaliduser";
    private String invalidPassword = "invalidPassword";

 @Before
    public void setUp() {
        driver = new HtmlUnitDriver();
        wait = new WebDriverWait(driver, 30); // Increased the timeout
    }

    @After
    public void tearDown() {
        if (driver != null) {
            driver.quit();
        }
    }

    @Test
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
        assertTrue(driver.findElement(By.xpath("//a[contains(text(),'" + validUsername + "')]")).isDisplayed());
    } catch (Exception e) {
        System.out.println("Exception: " + e.getMessage());
        System.out.println("Current URL: " + driver.getCurrentUrl());
        System.out.println("Page source: " + driver.getPageSource());
        throw e;
    }
}


    @Test
    public void testLoginWithInvalidCredentials() {
        try {
            driver.get(url);

            // Log the current URL for debugging
            System.out.println("Current URL before login: " + driver.getCurrentUrl());
            System.out.println("Page title: " + driver.getTitle());
            System.out.println("Page source: " + driver.getPageSource());

            wait.until(ExpectedConditions.presenceOfElementLocated(By.name("cust_username")));

            // Log presence of form elements
            System.out.println("Form and input elements are present.");

            driver.findElement(By.name("cust_username")).sendKeys(invalidUsername);
            driver.findElement(By.name("cust_pass")).sendKeys(invalidPassword);
            driver.findElement(By.id("submit")).click();

            // Log presence of error message
            System.out.println("Login form submitted with invalid credentials.");

            // Wait for the error popup
            wait.until(ExpectedConditions.visibilityOfElementLocated(By.className("swal2-title")));
            WebElement errorMessage = driver.findElement(By.className("swal2-title"));
            assertTrue(errorMessage.isDisplayed());
            System.out.println("Error message displayed: " + errorMessage.getText());
        } catch (Exception e) {
            System.out.println("Exception: " + e.getMessage());
            System.out.println("Current URL: " + driver.getCurrentUrl());
            System.out.println("Page source: " + driver.getPageSource());
            throw e;
        }
    }
}
