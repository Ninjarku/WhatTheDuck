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

    @Test
    public void testLoginWithInvalidCredentials() {
        try {
            driver.get(loginUrl);

            // Log the current URL for debugging
            System.out.println("Current URL before login: " + driver.getCurrentUrl());
            System.out.println("Page title: " + driver.getTitle());
            System.out.println("Page source: " + driver.getPageSource());

            wait.until(ExpectedConditions.presenceOfElementLocated(By.name("cust_username")));

            // Log presence of form elements
            System.out.println("Form and input elements are present.");

            // Log the credentials being sent
            System.out.println("Sending credentials: " + invalidUsername + " / " + invalidPassword);

            driver.findElement(By.name("cust_username")).sendKeys(invalidUsername);
            driver.findElement(By.name("cust_pass")).sendKeys(invalidPassword);
            driver.findElement(By.id("submit")).click();

            // Log presence of error message
            System.out.println("Login form submitted with invalid credentials.");

            // Wait for the error popup
            wait.until(ExpectedConditions.visibilityOfElementLocated(By.className("swal2-title")));
            System.out.println("Error popup is visible.");
        } catch (Exception e) {
            System.out.println("Exception: " + e.getMessage());
            System.out.println("Current URL: " + driver.getCurrentUrl());
            System.out.println("Page source: " + driver.getPageSource());
            throw e;
        }
    }

    @Test
    public void testLoginWithValidUsernameAndInvalidPassword() {
        try {
            driver.get(loginUrl);

            // Log the current URL for debugging
            System.out.println("Current URL before login: " + driver.getCurrentUrl());
            System.out.println("Page title: " + driver.getTitle());
            System.out.println("Page source: " + driver.getPageSource());

            wait.until(ExpectedConditions.presenceOfElementLocated(By.name("cust_username")));

            // Log presence of form elements
            System.out.println("Form and input elements are present.");

            // Log the credentials being sent
            System.out.println("Sending credentials: " + validUsername + " / " + invalidPassword);

            driver.findElement(By.name("cust_username")).sendKeys(validUsername);
            driver.findElement(By.name("cust_pass")).sendKeys(invalidPassword);
            driver.findElement(By.id("submit")).click();

            // Log presence of error message
            System.out.println("Login form submitted with valid username and invalid password.");

            // Wait for the error popup
            wait.until(ExpectedConditions.visibilityOfElementLocated(By.className("swal2-title")));
            System.out.println("Error popup is visible.");
        } catch (Exception e) {
            System.out.println("Exception: " + e.getMessage());
            System.out.println("Current URL: " + driver.getCurrentUrl());
            System.out.println("Page source: " + driver.getPageSource());
            throw e;
        }
    }

    @Test
    public void testLoginWithInvalidUsernameAndValidPassword() {
        try {
            driver.get(loginUrl);

            // Log the current URL for debugging
            System.out.println("Current URL before login: " + driver.getCurrentUrl());
            System.out.println("Page title: " + driver.getTitle());
            System.out.println("Page source: " + driver.getPageSource());

            wait.until(ExpectedConditions.presenceOfElementLocated(By.name("cust_username")));

            // Log presence of form elements
            System.out.println("Form and input elements are present.");

            // Log the credentials being sent
            System.out.println("Sending credentials: " + invalidUsername + " / " + validPassword);

            driver.findElement(By.name("cust_username")).sendKeys(invalidUsername);
            driver.findElement(By.name("cust_pass")).sendKeys(validPassword);
            driver.findElement(By.id("submit")).click();

            // Log presence of error message
            System.out.println("Login form submitted with invalid username and valid password.");

            // Wait for the error popup
            wait.until(ExpectedConditions.visibilityOfElementLocated(By.className("swal2-title")));
            System.out.println("Error popup is visible.");
        } catch (Exception e) {
            System.out.println("Exception: " + e.getMessage());
            System.out.println("Current URL: " + driver.getCurrentUrl());
            System.out.println("Page source: " + driver.getPageSource());
            throw e;
        }
    }

    @Test
    public void testSignupWithValidData() {
        driver.get(signupUrl);

        try {
            wait.until(ExpectedConditions.presenceOfElementLocated(By.name("cust_username")));

            // Fill in the signup form with valid data
            driver.findElement(By.name("cust_username")).sendKeys("newuser");
            driver.findElement(By.name("cust_email")).sendKeys("newuser@example.com");
            driver.findElement(By.name("cust_mobile")).sendKeys("1234567890");
            driver.findElement(By.name("cust_dob")).sendKeys("01011990");
            driver.findElement(By.name("cust_pass")).sendKeys(validPassword);
            driver.findElement(By.name("cust_confirm_pass")).sendKeys(validPassword);
            driver.findElement(By.id("submit")).click();

            // Wait for success message or redirection
            wait.until(ExpectedConditions.presenceOfElementLocated(By.className("success-message")));

            // Verify success
            WebElement successMessage = driver.findElement(By.className("success-message"));
            assertTrue(successMessage.isDisplayed());
            System.out.println("Signup success message is displayed.");

        } catch (Exception e) {
            System.out.println("Exception: " + e.getMessage());
            System.out.println("Current URL: " + driver.getCurrentUrl());
            System.out.println("Page source: " + driver.getPageSource());
            throw e;
        }
    }

    @Test
    public void testSignupWithMissingFields() {
        driver.get(signupUrl);

        try {
            wait.until(ExpectedConditions.presenceOfElementLocated(By.name("cust_username")));

            // Fill in the signup form with missing data (leave email empty)
            driver.findElement(By.name("cust_username")).sendKeys("newuser");
            driver.findElement(By.name("cust_mobile")).sendKeys("1234567890");
            driver.findElement(By.name("cust_dob")).sendKeys("01011990");
            driver.findElement(By.name("cust_pass")).sendKeys(validPassword);
            driver.findElement(By.name("cust_confirm_pass")).sendKeys(validPassword);
            driver.findElement(By.id("submit")).click();

            // Wait for error message
            wait.until(ExpectedConditions.presenceOfElementLocated(By.className("error-message")));

            // Verify error
            WebElement errorMessage = driver.findElement(By.className("error-message"));
            assertTrue(errorMessage.isDisplayed());
            System.out.println("Signup error message is displayed.");

        } catch (Exception e) {
            System.out.println("Exception: " + e.getMessage());
            System.out.println("Current URL: " + driver.getCurrentUrl());
            System.out.println("Page source: " + driver.getPageSource());
            throw e;
        }
    }

    @Test
    public void testSignupWithInvalidData() {
        driver.get(signupUrl);

        try {
            wait.until(ExpectedConditions.presenceOfElementLocated(By.name("cust_username")));

            // Fill in the signup form with invalid data
            driver.findElement(By.name("cust_username")).sendKeys("newuser");
            driver.findElement(By.name("cust_email")).sendKeys("invalid-email");
            driver.findElement(By.name("cust_mobile")).sendKeys("invalid-mobile");
            driver.findElement(By.name("cust_dob")).sendKeys("invalid-date");
            driver.findElement(By.name("cust_pass")).sendKeys("short");
            driver.findElement(By.name("cust_confirm_pass")).sendKeys("mismatch");
            driver.findElement(By.id("submit")).click();

            // Wait for error message
            wait.until(ExpectedConditions.presenceOfElementLocated(By.className("error-message")));

            // Verify error
            WebElement errorMessage = driver.findElement(By.className("error-message"));
            assertTrue(errorMessage.isDisplayed());
            System.out.println("Signup error message is displayed.");

        } catch (Exception e) {
            System.out.println("Exception: " + e.getMessage());
            System.out.println("Current URL: " + driver.getCurrentUrl());
            System.out.println("Page source: " + driver.getPageSource());
            throw e;
        }
    }

    @Test
    public void testSignupWithMismatchedPasswords() {
        driver.get(signupUrl);

        try {
            wait.until(ExpectedConditions.presenceOfElementLocated(By.name("cust_username")));

            // Fill in the signup form with mismatched passwords
            driver.findElement(By.name("cust_username")).sendKeys("newuser");
            driver.findElement(By.name("cust_email")).sendKeys("newuser@example.com");
            driver.findElement(By.name("cust_mobile")).sendKeys("1234567890");
            driver.findElement(By.name("cust_dob")).sendKeys("01011990");
            driver.findElement(By.name("cust_pass")).sendKeys(validPassword);
            driver.findElement(By.name("cust_confirm_pass")).sendKeys("differentPassword");
            driver.findElement(By.id("submit")).click();

            // Wait for error message
            wait.until(ExpectedConditions.presenceOfElementLocated(By.className("error-message")));

            // Verify error
            WebElement errorMessage = driver.findElement(By.className("error-message"));
            assertTrue(errorMessage.isDisplayed());
            System.out.println("Signup error message is displayed.");

        } catch (Exception e) {
            System.out.println("Exception: " + e.getMessage());
            System.out.println("Current URL: " + driver.getCurrentUrl());
            System.out.println("Page source: " + driver.getPageSource());
            throw e;
        }
    }
}
