<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Tabel untuk document OnlyOffice
        Schema::create('only_office_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('filename');
            $table->string('file_path');
            $table->string('file_type'); // xlsx, docx, pptx
            $table->bigInteger('file_size');
            $table->string('key')->unique(); // Unique key untuk OnlyOffice
            $table->json('metadata')->nullable();
            $table->string('status')->default('draft'); // draft, editing, saved
            $table->timestamp('last_modified')->nullable();
            $table->timestamps();
        });

        // Tabel untuk version control
        Schema::create('document_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('only_office_documents')->onDelete('cascade');
            $table->integer('version');
            $table->string('file_path');
            $table->foreignId('created_by')->constrained('users');
            $table->text('changes_summary')->nullable();
            $table->timestamp('created_at');
        });

        // Tabel untuk sharing
        Schema::create('document_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('only_office_documents')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('permission', ['view', 'edit', 'comment']);
            $table->timestamps();

            $table->unique(['document_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('document_shares');
        Schema::dropIfExists('document_versions');
        Schema::dropIfExists('only_office_documents');
    }
};
