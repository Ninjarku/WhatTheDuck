import org.junit.After;
import org.junit.Before;
import org.junit.Test;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.chrome.ChromeOptions;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import static org.junit.Assert.assertTrue;

public class AppTest {

    private WebDriver driver;
    private WebDriverWait wait;
    private String loginUrl = "https://whattheduck.ddns.net/Login.php";
    private String signupUrl = "https://whattheduck.ddns.net/Signup.php";
    private String validUsername = System.getenv("TEST_USERNAME");
    private String validPassword = System.getenv("TEST_PASSWORD");
    private String invalidUsername = "invaliduser";
    private String invalidPassword = "invalidPassword";

    @Before
    public void setUp() {
        // Set the path to your ChromeDriver executable
        System.setProperty("webdriver.chrome.driver", "/usr/local/bin/chromedriver");

        // Set ChromeOptions to run Chrome in headless mode
        ChromeOptions options = new ChromeOptions();
        options.addArguments("--headless");
        options.addArguments("--no-sandbox");
        options.addArguments("--disable-dev-shm-usage");

        driver = new ChromeDriver(options);
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
        driver.get(loginUrl);

        try {
            wait.until(ExpectedConditions.presenceOfElementLocated(By.name("cust_username")));
            driver.findElement(By.name("cust_username")).sendKeys(validUsername);
            driver.findElement(By.name("cust_pass")).sendKeys(validPassword);
            driver.findElement(By.id("submit")).click();

            // Check for the success button in the popup
            wait.until(ExpectedConditions.visibilityOfElementLocated(By.xpath("//button[text()='Return to Home']")));
            WebElement successButton = driver.findElement(By.xpath("//button[text()='Return to Home']"));
            assertTrue(successButton.isDisplayed());
        } catch (Exception e) {
            throw new RuntimeException(e);
        }
    }

    @Test
    public void testLoginWithInvalidCredentials() {
        driver.get(loginUrl);

        try {
            wait.until(ExpectedConditions.presenceOfElementLocated(By.name("cust_username")));
            driver.findElement(By.name("cust_username")).sendKeys(invalidUsername);
            driver.findElement(By.name("cust_pass")).sendKeys(invalidPassword);
            driver.findElement(By.id("submit")).click();

            wait.until(ExpectedConditions.visibilityOfElementLocated(By.className("swal2-title")));
        } catch (Exception e) {
            throw new RuntimeException(e);
        }
    }

    @Test
    public void testLoginWithValidUsernameAndInvalidPassword() {
        driver.get(loginUrl);

        try {
            wait.until(ExpectedConditions.presenceOfElementLocated(By.name("cust_username")));
            driver.findElement(By.name("cust_username")).sendKeys(validUsername);
            driver.findElement(By.name("cust_pass")).sendKeys(invalidPassword);
            driver.findElement(By.id("submit")).click();

            wait.until(ExpectedConditions.visibilityOfElementLocated(By.className("swal2-title")));
        } catch (Exception e) {
            throw new RuntimeException(e);
        }
    }

    @Test
    public void testLoginWithInvalidUsernameAndValidPassword() {
        driver.get(loginUrl);

        try {
            wait.until(ExpectedConditions.presenceOfElementLocated(By.name("cust_username")));
            driver.findElement(By.name("cust_username")).sendKeys(invalidUsername);
            driver.findElement(By.name("cust_pass")).sendKeys(validPassword);
            driver.findElement(By.id("submit")).click();

            wait.until(ExpectedConditions.visibilityOfElementLocated(By.className("swal2-title")));
        } catch (Exception e) {
            throw new RuntimeException(e);
        }
    }
    @Test
    public void testLogintoFullPaymentWithValidCredentials() {
        driver.get(loginUrl);

        try {
            wait.until(ExpectedConditions.presenceOfElementLocated(By.name("cust_username")));
            driver.findElement(By.name("cust_username")).sendKeys(validUsername);
            driver.findElement(By.name("cust_pass")).sendKeys(validPassword);
            driver.findElement(By.id("submit")).click();

            // Wait for the "Return to Home" button and click it
            System.out.println("Waiting for 'Return to Home' button...");
            wait.until(ExpectedConditions.presenceOfElementLocated(By.xpath("//button[text()='Return to Home']")));
            WebElement returnToHomeButton = driver.findElement(By.xpath("//button[text()='Return to Home']"));
            returnToHomeButton.click();

            System.out.println("Verifying username is displayed on index page...");
            wait.until(ExpectedConditions.presenceOfElementLocated(By.xpath("//a[contains(text(),'" + validUsername + "')]")));
            WebElement usernameLink = driver.findElement(By.xpath("//a[contains(text(),'" + validUsername + "')]"));
            assertTrue(usernameLink.isDisplayed());
            System.out.println("Username is displayed: " + usernameLink.getText());

            System.out.println("Clicking 'Add To Cart' button...");
            WebElement addToCartButton = driver.findElement(By.xpath("//button[text()='Add To Cart']"));
            addToCartButton.click();

            System.out.println("Navigating to cart page...");

            System.out.println("Selecting product checkbox...");
            WebElement productCheckbox = wait.until(ExpectedConditions.presenceOfElementLocated(By.cssSelector("input[type='checkbox']")));
            productCheckbox.click();

            System.out.println("Clicking 'Proceed to checkout' button...");
            WebElement proceedToCheckoutButton = driver.findElement(By.xpath("//button[text()='Proceed to checkout']"));
            proceedToCheckoutButton.click();
            
            System.out.println("Navigating to checkout page...");

            System.out.println("Clicking 'Proceed to payment' button...");
            WebElement proceedToPaymentButton = driver.findElement(By.id("checkout-btn"));
            proceedToPaymentButton.click();

            System.out.println("Navigating to payment page...");
    
            System.out.println("Filling out payment form...");
            wait.until(ExpectedConditions.presenceOfElementLocated(By.name("full_name")));
            driver.findElement(By.name("fullName")).sendKeys("John Doe");
            driver.findElement(By.name("phoneNumber")).sendKeys("123456789");
            driver.findElement(By.name("address")).sendKeys("1234 Duck Street");
            driver.findElement(By.name("postalCode")).sendKeys("123456");
            driver.findElement(By.name("unitNo")).sendKeys("12A");
            System.out.println("Selecting payment method...");
            Select paymentMethodDropdown = new Select(driver.findElement(By.name("payment_method")));
            paymentMethodDropdown.selectByVisibleText("Credit/Debit Card");

            System.out.println("Clicking 'Pay Now' button...");
            WebElement payNowButton = driver.findElement(By.id("pay-now-btn"));
            payNowButton.click();

            // Add assertions as necessary to verify the payment process
            System.out.println("Test completed successfully.");

        } catch (Exception e) {
            System.out.println("Test failed: " + e.getMessage());
            throw new RuntimeException(e);
        }
    }
}
