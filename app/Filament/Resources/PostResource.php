<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Category;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Create post')
                    ->description('create post over here.')
                    ->collapsible()
                    ->schema([
                        TextInput::make('title')->required(),
                        TextInput::make('slug')->required(),
                        Select::make('category_id')
                            ->options(Category::all()->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->label('Category'),
                        ColorPicker::make('color')->required(),
                        MarkdownEditor::make('content')->required()->columnSpanFull(),
                    ])->columnSpan(2)->columns(2),

                Group::make()
                    ->schema([
                        Section::make('Image')
                            ->collapsed()
                            ->schema([
                                FileUpload::make('thumbnail')
                                    ->disk('public')
                                    ->directory('thumbnails')
                                    ->required(),
                            ])->columnSpan(1),
                        Section::make('Meta')
                            ->schema([
                                TagsInput::make('tags')->required(),
                                Checkbox::make('published')->required(),
                            ])
                    ]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),
                ImageColumn::make('thumbnail'),
                ColorColumn::make('color'),
                TextColumn::make('title'),
                TextColumn::make('slug'),
                TagsColumn::make('tags'),
                TextColumn::make('category.name'),
                CheckboxColumn::make('published'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
