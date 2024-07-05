import org.junit.After;
import org.junit.Before;
import org.junit.Test;
import static org.junit.Assert.*;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.htmlunit.HtmlUnitDriver;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

public class AppTest {
    private WebDriver driver;
    private WebDriverWait wait;

    private String url = "https://whattheduck.ddns.net/Login.php";  
    private String validUsername = System.getenv("TEST_USERNAME");
    private String validPassword = System.getenv("TEST_PASSWORD");
    private String invalidPassword = "invalid_password";

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

            // wait until page is loaded or timeout error
            wait.until(ExpectedConditions.presenceOfElementLocated(By.name("cust_username")));

            // enter input
            driver.findElement(By.name("cust_username")).sendKeys(validUsername);
            driver.findElement(By.name("cust_pass")).sendKeys("invalidPassword");
            // click submit
            driver.findElement(By.id("submit")).click();

            // check result: verify error message is displayed
            wait.until(ExpectedConditions.visibilityOfElementLocated(By.className("swal2-title")));
            WebElement errorMessage = driver.findElement(By.className("swal2-title"));
            assertTrue(errorMessage.isDisplayed());
        } catch (Exception e) {
            System.out.println("Exception: " + e.getMessage());
            System.out.println("Current URL: " + driver.getCurrentUrl());
            System.out.println("Page source: " + driver.getPageSource());
            throw e;
        }
    }
}
