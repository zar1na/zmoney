<?php

namespace App\Http\Controllers;

use App\Blog;
use App\Events\BlogCreated;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class BlogsController extends Controller
{
    // now only logged in users can view blogs
    public function __construct()
    {
         $this->middleware('auth')->except(['index', 'show']);
         // the user must be logged in to create (etc) but can see the blogs in detail
         // $this->middleware('auth');
    }
    
    public function index()
    {
       $blogs = Blog::all();
        // using Eloquent which is Laravel's active record implementation
        
        //$blogs = Blog::where('owner_id', auth()->id())->get(); // select * from blogs where owner_id = current user
        // ^ allows the authenticated user to see their own blogs using auth-id which checkes the instance and returns it's records.
        
        return view('blogs.index', compact ('blogs'));
    }

    public function create()
    {
        return view('blogs.create');
    }

    public function store()
    {
        $attributes = request()->validate([ // validate method
        'title' => ['required', 'min:3'],
        'description' => ['required', 'min:3']
        ]); // if the validation fails it will automatically redirect and store nothing in the database
        
        $attributes['owner_id'] = auth()->id();
        
        $blog = Blog::create($attributes);
        
        //firing off a custom event
        event(new BlogCreated($blog));
        
        // fires off a notification when a blog is created
       // $user->notify(new NewBlogCreated);
      // Notification::send(new BlogCreated($blog));
        
        // validate the blog and save the blog
        session()->flash('created', 'Your blog has been created!');
        // stores for single request and if refreshed it will no longer be there
        
        //session(['created' => 'The blog was created' ]);
        // stores for the lifetime of the session
        
        return redirect('/blogs'); // ->with(['created' => 'The blog was created' ]);;
        // with flashes the message
    }

    public function show(Blog $blog)
    {
        //$this->authorize('update', $blog);
        // authorize using policy gives the same results
        // abort_unless(auth()->owns($blog), 403);
        //abort_if(\Gate::denies('update', $blog), 403);
        
        return view('blogs.show', compact('blog'));
        
    }

    public function edit(Blog $blog)
    {
        //$blog = Blog::find($id);
        
        // GATE
        /* if ($blog->owner_id !== auth()->id()) {
            abort (403);
        } */
       // abort_if(\Gate::denies('update', $blog), 403);
        
        $this->authorize('edit', $blog);
        
     return view('blogs.edit', compact('blog'));
    }

    public function update(Blog $blog)
    {
        $blog->update(request(['title', 'description']));
        session()->flash('updated', 'The blog was updated!');
        return view('blogs.show', compact('blog'));
    }

    public function destroy(Blog $blog)
    {
        $blog->delete();
        session()->flash('deleted', 'Your blog was deleted!');
        return redirect('/blogs');
    }
}
