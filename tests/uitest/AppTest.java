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
        wait = new WebDriverWait(driver, 20);
    }

    @After
    public void tearDown() {
        if (driver != null) {
            driver.quit();
        }
    }

    @Test
    public void testLoginWithValidCredentials() {
        try {
            driver.get(url);

            // Log the current URL for debugging
            System.out.println("Current URL before login: " + driver.getCurrentUrl());
            System.out.println("Page title: " + driver.getTitle());
            System.out.println("Page source: " + driver.getPageSource());

            wait.until(ExpectedConditions.presenceOfElementLocated(By.name("cust_username")));

            // Log presence of form elements
            System.out.println("Form and input elements are present.");

            driver.findElement(By.name("cust_username")).sendKeys(validUsername);
            driver.findElement(By.name("cust_pass")).sendKeys(validPassword);
            driver.findElement(By.id("submit")).click();

            // Log presence of success popup
            System.out.println("Login form submitted.");

            // Wait for the success popup and click "Return to Home"
            wait.until(ExpectedConditions.visibilityOfElementLocated(By.xpath("//button[@class='swal2-confirm swal2-styled' and text()='Return to Home']")));
            System.out.println("Success popup is visible.");

            driver.findElement(By.xpath("//button[@class='swal2-confirm swal2-styled' and text()='Return to Home']")).click();
            System.out.println("Clicked 'Return to Home'.");

            // Log the current URL and HTML content for debugging
            System.out.println("Current URL after clicking 'Return to Home': " + driver.getCurrentUrl());
            System.out.println("Page source after clicking 'Return to Home': " + driver.getPageSource());

            // Verify that the page redirects to the home page after clicking the button
            assertTrue(wait.until(ExpectedConditions.urlContains("index.php")));
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
