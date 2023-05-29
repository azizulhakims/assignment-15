 
Task 1: Request Validation
Implement request validation for a registration form that contains the following fields: name, email, and password. Validate the following rules:
 
name: required, string, minimum length 2.
email: required, valid email format.
password: required, string, minimum length 8.
Ans: 

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegistrationFormRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|min:2',
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ];
    }
}

public function messages()
{
    return [
        'name.required' => 'The name field is required.',
        'name.min' => 'The name must be at least :min characters long.',
        'email.required' => 'The email field is required.',
        'email.email' => 'The email must be a valid email address.',
        'password.required' => 'The password field is required.',
        'password.min' => 'The password must be at least :min characters long.',
    ];
}

use App\Http\Requests\RegistrationFormRequest;

public function register(RegistrationFormRequest $request)
{
    // The request data is already validated at this point
    // You can access the validated data using $request->validated()

    // Perform the registration logic here
}


 
Task 2: Request Redirect
Create a route /home that redirects to /dashboard using a 302 redirect.
Ans: 
Route::get('/home', function () {
    return redirect('/dashboard');
});

 
Task 3: Global Middleware
Create a global middleware that logs the request method and URL for every incoming request. Log the information to the Laravel log file.
Ans:
<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Log;
class LogRequestMiddleware
{
    public function handle($request, Closure $next)
    {
        Log::info('Request: ' . $request->method() . ' ' . $request->fullUrl());
        return $next($request);
    }
}

	In kernel 
protected $middleware = [
    // other middleware...
    \App\Http\Middleware\LogRequestMiddleware::class,
];

Task 4: Route Middleware
Create a route group for authenticated users only. This group should include routes for /profile and /settings. Apply a middleware called AuthMiddleware to the route group to ensure only authenticated users can access these routes.
Ans:
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', function () {
        // Route logic for /profile
    });
    Route::get('/settings', function () {
        // Route logic for /settings
    });
});
Task 5: Controller
Create a controller called ProductController that handles CRUD operations for a resource called Product. Implement the following methods:
 
index(): Display a list of all products.
create(): Display the form to create a new product.
store(): Store a newly created product.
edit($id): Display the form to edit an existing product.
update($id): Update the specified product.
destroy($id): Delete the specified product.
Ans: 
<?php
namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Http\Request;
class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('products.index', ['products' => $products]);
    }
    public function create()
    {
        return view('products.create');
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'description' => 'required',
        ]);
        Product::create($validatedData);
        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.');
    }
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return view('products.edit', ['product' => $product]);
    }
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'description' => 'required',
        ]);
        Product::where('id', $id)->update($validatedData);
        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }
    public function destroy($id)
    {
        Product::findOrFail($id)->delete();
        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }
}

Task 6: Single Action Controller
Create a single action controller called ContactController that handles a contact form submission. Implement the __invoke() method to process the form submission and send an email to a predefined address with the submitted data.
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function __invoke(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required',
        ]);

        // Send email
        $recipientEmail = 'your-email@example.com'; // Replace with the actual recipient email address
        Mail::to($recipientEmail)->send(new \App\Mail\ContactFormMail($validatedData));

        return redirect()->back()->with('success', 'Thank you for your message. We will get back to you soon.');
    }
}

use App\Http\Controllers\ContactController;

Route::post('/contact', ContactController::class)->name('contact.submit');

<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        return $this->markdown('emails.contact')
            ->subject('New Contact Form Submission');
    }
}
 
Task 7: Resource Controller
Create a resource controller called PostController that handles CRUD operations for a resource called Post. Ensure that the controller provides the necessary methods for the resourceful routing conventions in Laravel.
<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::all();

        return view('posts.index', ['posts' => $posts]);
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required',
            'content' => 'required',
        ]);

        Post::create($validatedData);

        return redirect()->route('posts.index')
            ->with('success', 'Post created successfully.');
    }

    public function show($id)
    {
        $post = Post::findOrFail($id);

        return view('posts.show', ['post' => $post]);
    }

    public function edit($id)
    {
        $post = Post::findOrFail($id);

        return view('posts.edit', ['post' => $post]);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'title' => 'required',
            'content' => 'required',
        ]);

        $post = Post::findOrFail($id);
        $post->update($validatedData);

        return redirect()->route('posts.index')
            ->with('success', 'Post updated successfully.');
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return redirect()->route('posts.index')
            ->with('success', 'Post deleted successfully.');
    }
}
 
Task 8: Blade Template Engine
Create a Blade view called welcome.blade.php that includes a navigation bar and a section displaying the text "Welcome to Laravel!".
<!DOCTYPE html>
<html>
<head>
    <title>Welcome to Laravel</title>
    <!-- Add your CSS stylesheets or links here -->
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="#">About</a></li>
                <li><a href="#">Contact</a></li>
            </ul>
        </nav>
    </header>

    <section>
        <h1>Welcome to Laravel!</h1>
    </section>

    <!-- Add your JavaScript scripts or links here -->
</body>
</html>
 
 

