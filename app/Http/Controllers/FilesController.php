<?php

namespace App\Http\Controllers;

use App\Models\files;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FilesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        $files = files::orderByRaw("type <> 'folder'")->where('user_id', Auth::id())->where('parent_id', null)->get();
        return view('home', compact('files'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $user_name = str_replace(' ', '_', Auth::user()->name);

       
        $var = explode('/', $request->server('HTTP_REFERER'));
        $num = (int)end($var);
        $find = files::find($num);

        if ($find === null) :
            if ($request->post('folder')) :
                $folder = new files();
                $folder->name = $request->post('folder');
                $folder->parent_id = null;
                $folder->user_id = Auth::id();
                $folder->type = 'folder';
                $folder->file_path = $user_name . '/' . $folder->name;
                $folder->save();
            endif;
            if ($request->hasFile('files')) :
                foreach ($request->file('files') as $file) :
                    $name = $file->getClientOriginalName();
                    $path = $file->store($user_name, [
                        'disk' => 'uploads',
                    ]);
                    $newFile = new files();
                    $newFile->name = $name;
                    $newFile->parent_id = null;
                    $newFile->user_id = Auth::id();
                    $newFile->type = 'file';
                    $newFile->file_path = $path;
                    $newFile->file_size = $file->getSize();
                    $newFile->file_type = $file->getMimeType();
                    $newFile->save();
                endforeach;
            endif;
            return redirect()->back();
        else:
            if ($request->post('folder')) :
                $parent_folder = $find->parent();
                $parent_folder_path = $parent_folder->first()->file_path;
                $folder = new files();
                $folder->name = $request->post('folder');
                $folder->parent_id = $num;
                $folder->user_id = Auth::id();
                $folder->type = 'folder';

                $folder->file_path = $parent_folder_path . '/' . $find->name . '/' . $folder->name;

                $folder->save();
            endif;

            if ($request->hasFile('files')) :
                foreach ($request->file('files') as $file):
                    $name = $file->getClientOriginalName();
                    $parent_folder = $find->parent();

                    if ($parent_folder->count() > 0) :
                        $parent_folder = $find->parent();
                        $parent_folder_name = $parent_folder->first()->name;
                        $parent_folder_path = $parent_folder->first()->file_path;

                        $path = $file->store($find->file_path, [
                            'disk' => 'uploads',
                        ]);

                    else:
                        $path = $file->store($user_name . '/' . $find->name, [
                            'disk' => 'uploads',
                        ]);
                    endif;

                    $newFile = new files();
                    $newFile->name = $name;
                    $newFile->parent_id = $num;
                    $newFile->user_id = Auth::id();
                    $newFile->type = 'file';
                    $newFile->file_path = $path;
                    $newFile->file_size = $file->getSize();
                    $newFile->file_type = $file->getMimeType();

                    $newFile->save();
                endforeach;
            endif;
            return redirect()->back();
        endif;

    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\files $files
     * @return Response
     */
    public function show($id)
    {
        //
        $files = files::where('parent_id', $id)->get();
        $parent_id = files::where('id', $id)->first();
        // dd($parent_id);
        return view('show', compact('files', 'parent_id'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\files $files
     * @return Response
     */
    public function edit(files $files)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param \App\Models\files $files
     * @return Response
     */
    public function update(Request $request, files $files)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $file = files::findOrFail($id);
        $children = $file->children();
        if (count($children->get()) > 0):

            foreach ($children->get() as $child):
                $file_path = $child->file_path;

                if (Storage::disk('uploads')->exists($file_path)):
                    if ($child->type === 'folder'):

                        Storage::disk('uploads')->deleteDirectory($file_path);
                        Storage::disk('uploads')->delete($file_path);
                        // Delete Child folder content
                        $child->children()->forceDelete();
                    else:
                        Storage::disk('uploads')->delete($file_path);
                    ;
                    endif;

                endif;

            endforeach;

           
            $children->forceDelete();

        endif;

        $file_path = $file->file_path;
        if (Storage::disk('uploads')->exists($file->file_path)):

            if ($file->type === 'folder'):
                Storage::disk('uploads')->deleteDirectory($file_path);

            else:
                Storage::disk('uploads')->delete($file_path);
   
            endif;
        endif;
        $file->forceDelete();
        return redirect()->back();

    }
}
