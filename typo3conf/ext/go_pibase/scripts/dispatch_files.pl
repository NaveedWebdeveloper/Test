#!/usr/bin/perl -w

use strict;


# Configurable constants and parameter lists ----------------------------------

use constant NICELEVEL              => 19;  # Nice level to execute tools with
use constant ALLOW_EXTENSIONLESS    => 1;   # Whether filenames without extensions are allowed
use constant USE_POSITIVES          => 0;   # Whether to use positive lists for file extensions
use constant USE_NEGATIVES          => 1;   # Whether to use negative lists for file extensions
use constant EXT_INFOFILE           => ".txt"; # Extension for info files
use constant EXT_SCREENSHOT         => ".jpg"; # Extension for screenshot files
use constant SPLIT_FILENAME_DEPTH   => 0;   # Subdirectory level in which to expect actual files
use constant SPLIT_FILENAME_SEGLEN  => 3;   # Length of directory name segment of filename
use constant INFOFILE_ON_ERROR      => 1;   # Boolean, whether to create info files for erroneous source files as well
use constant CREATE_THUMBNAILS      => 0;   # Create additional small thumbnail image of incoming photos


my %path = (                            # Definition of all paths
    'base'          =>  $ARGV[11], 	 # Base working directory
    'incoming'      =>  'source/',                    		# Raw files from visitors, various file formats
    'result'        =>  ['result', ''],    		    		# Resulting FLV files, ready to be downloaded
    'processed'     =>  ['processed/', 'done/'],  		# Raw files that were processed successfully
    'unrecognized'  =>  ['crashed/', 'not_def/'],			# Raw files that seem to be valid files but could not be recognized
    'crap'          =>  ['crashed/', 'crashed/'],           	# Raw files that don't seem to be valid files
    'sshot',        =>  ['screenshots/', ''],				# Generated screenshots
    'infofile'	    =>	'infofiles/',							# Generated infofiles
    'video'         =>  '',            						# Video files
    'audio',        =>  'mp3/',        						# Sound files (MP3s)
    'photo',        =>  '',            						# Photo files
    'cache'	    =>  '../cache/',
    'logfile'       =>  'log/dispatch_files.log');

my %tool = (                            # Definition of tools to use
    'ffmpeg'        => '/usr/bin/ffmpeg',
    'lame'          => '/usr/bin/lame',
    'sox'           => '/usr/bin/sox',
    'imgconvert'    => '/usr/bin/convert',
    'magic'         => 'file');

my %parm_video = (                      # Definition of video parameters
    'width'         => [160, $ARGV[0]], 							# Widths in pixel
    'height'        => [120, $ARGV[1]],    						 	#Heights in pixel $ARGV[1]
    'bitrate'       => [192, $ARGV[2]],     						# Bitrates in kbit/sec
    'framerate'     => [15,  $ARGV[3]],    						 	# Framerates in fps
    'quality'       => [6,   0],        							# Qualities as quantizer value
    'audiorate'     => [48,  $ARGV[4]],    							# Audio bitrate in kbits/sec
    'audiofreq'     => [11025, $ARGV[5]],   						# Audio sampling frequency in Hz
    'maxduration'   => ['00:00:10', $ARGV[6]], 						# Maximum durations in sec
    'maxfilesize'   => ['2m', '80m'],   							# Maximum file sizes
    'custom'        => ['-g 250 -bt 20000', '-g 250 -bt 20000']); 	# Custom extra parameters for ffmpeg

my %parm_sshot = (                      # Definition of screenshot parameters
    'time'          => ["00:00:01", $ARGV[7]], 		# Time after which screenshot is to be taken
    'qual',         => [0.75, $ARGV[8]],     		# JPEG qualities
    'width'         => [160, $ARGV[9]],      		# Widths in pixel
    'height'        => [120, $ARGV[10]]);     		# Heights in pixel
	
my %parm_thumbnail = (        # Definition of thumbnail parameters
    'width'         => [160, $ARGV[14]],      		# Widths in pixel
    'height'        => [120, $ARGV[15]]);     		# Heights in pixel
	


my %parm_photo = (                      # Definition of photo parameters
    'qual'          => [0.75, 0.9],     			# JPEG qualities
    'width'         => [160, 320],      			# Widths in pixels
    'height'        => [120, 240],      			# Heights in pixel
    'extra'         => ['-bordercolor "#FFFFFF" -border 60 -gravity center -crop 160x120+0+0 +repage',
                        '-bordercolor "#FFFFFF" -border 60 -gravity center -crop 320x240+0+0 +repage']);

my %parm_thumb = (                      # Definition of photo parameters
    'qual'          => [0.75, 0.9],     		# JPEG qualities
    'width'         => [40, 80],        		# Widths in pixels
    'height'        => [30, 60]);       		# Heights in pixel

my %parm_audio = (
    'bitrate'       => [48, 96],        # Bitrates in kbit/sec
    'duration'      => [10, 30],        # Maximum durations
    'fadeout'       => [2, 3],          # Fadeout times
    'quality'       => [2, 0],          # Encoding quality
    'freq'          => [11025, 22050]); # Sample frequency in Hz

my %list_ext_pos = (                    # positive list of acceptable file extensions
    'video' => ['avi', 'mov', 'mpg', 'mpeg', 'mp4', 'mp3', '3gp', 'vob', 'asf', 'gif', 'jpg', 'jpeg', 'png', 'wma', 'wmv', 'rm'],
    'photo' => ['bmp', 'gif', 'png', 'jpg', 'jpeg'],
    'audio' => ['mp2', 'mp3', 'wav', 'au']);

my %list_ext_neg = (                    # Negative list of unacceptable file extensions
    'video' => ['bup', 'con', 'css', 'dat', 'dll', 'dmg', 'doc', 'ees', 'exe', 'fb', 'htm', 'ico', 'ifo', 'ini', 'jpx', 'lng', 'lnk', 'log', 'mdi', 'mswmm', 'odt', 'pdf', 'pkg', 'plf', 'pln', 'pls', 'pps', 'ppt', 'prj', 'ram', 'rar', 'rtf', 'sav', 'sbz', 'scn', 'stu', 'svg', 'swf', 'theme', 'thm', 'tmp', 'torrent', 'txt', 'vi', 'w3g', 'wpj', 'wpl', 'wvx', 'zip'],
    'photo' => ['bup', 'con', 'css', 'dat', 'dll', 'dmg', 'doc', 'ees', 'exe', 'fb', 'htm', 'ico', 'ifo', 'ini', 'jpx', 'lng', 'lnk', 'log', 'mdi', 'mswmm', 'odt', 'pdf', 'pkg', 'plf', 'pln', 'pls', 'pps', 'ppt', 'prj', 'ram', 'rar', 'rtf', 'sav', 'sbz', 'scn', 'stu', 'svg', 'swf', 'theme', 'thm', 'tmp', 'torrent', 'txt', 'vi', 'w3g', 'wpj', 'wpl', 'wvx', 'zip'],
    'audio' => ['bup', 'con', 'css', 'dat', 'dll', 'dmg', 'doc', 'ees', 'exe', 'fb', 'htm', 'ico', 'ifo', 'ini', 'jpx', 'lng', 'lnk', 'log', 'mdi', 'mswmm', 'odt', 'pdf', 'pkg', 'plf', 'pln', 'pls', 'pps', 'ppt', 'prj', 'ram', 'rar', 'rtf', 'sav', 'sbz', 'scn', 'stu', 'svg', 'swf', 'theme', 'thm', 'tmp', 'torrent', 'txt', 'vi', 'w3g', 'wpj', 'wpl', 'wvx', 'zip']);

my %min_filesize_incoming = (
    'video' => 5000,
    'photo' => 1000,
    'audio' => 10000);

my %min_filesize_processed = (
    'video' => 10000,
    'photo' => 1000,
    'audio' => 10000);

my $variant         = 1;                # Which quality variant to use for encoding



# Global variables ------------------------------------------------------------

my $videos_processed    = 0;
my $videos_error        = 0;
my $photos_processed    = 0;
my $photos_error        = 0;
my $audio_processed     = 0;
my $audio_error         = 0;



# Function declarations -------------------------------------------------------

sub analyze_ffmpeg_output ($);
sub check_file_type ($$);
sub cleanup_directory ($$);
sub create_info_file ($$$$$);
sub create_screenshot ($$);
sub ensure_target_directory ($$);
sub evaluate_video ($$$$);
sub get_all_files_in_path ($);
sub get_basename ($);
sub get_directory_entries ($);
sub get_ffmpeg_error_text ($);
sub get_file_ext ($);
sub get_filename_wo_ext ($);
sub get_file_magic ($);
sub get_file_size ($);
sub get_file_type_by_ext ($);
sub get_number ($);
sub get_subdir_list ($$);
sub get_time_string ($);
sub is_in_array ($$);
sub is_resolution ($);
sub logger ($);
sub move_source_file ($$$$);
sub process_all_files_in_path ($);
sub process_all_videos ();
sub process_audio ($$);
sub process_photo ($$);
sub process_video ($$);



# Program entry point ---------------------------------------------------------

# process_all_videos ();
process_video ($ARGV[12], $ARGV[13]);

#process_all_files_in_path ($path{'base'}.$path{'incoming'});



# Function implementations ----------------------------------------------------



# Get file extension ----------------------------------------------------------

sub get_file_ext ($) {
    return substr ($_[0], rindex ($_[0], '.') + 1);
}


# Get filename without extension ----------------------------------------------

sub get_filename_wo_ext ($) {
    return substr ($_[0], 0, rindex ($_[0], '.'));
}


# Get basename of filename ----------------------------------------------------

sub get_basename ($) {
    return rindex ($_[0], '/') >= 0 ? substr ($_[0], rindex ($_[0], '/')) : $_[0];
}


# Check whether a value exists in an array ------------------------------------

sub is_in_array ($$) {
 my ($array, $val) = @_;

    foreach (@$array) {
        return 1 if $_ eq $val; }
    return 0;
}


# Get string of current time of day -------------------------------------------

sub get_time_string ($) {
 my @time;

    @time = $_[0] ? localtime ($_[0]) : localtime ();
    return sprintf ("%04u-%02u-%02u %02u:%02u:%02u", $time[5] + 1900, $time[4] + 1, $time[3], $time[2], $time[1], $time[0]);
}


# Write a message to generic log ----------------------------------------------

sub logger ($) {
 local *FH;

    open FH, ">>", $path{'logfile'} || die ("Cannot write log file ".$path{'logfile'}." ".$!);
    print FH get_time_string (0).": ".$_[0]."\n";
    close FH;
}


# Get numeric value from string, possibly prepended by "K", "M" or "G" --------

sub get_number ($) {
 my ($number) = @_;
 my $lastchar;

    $lastchar = lc (substr ($number, length ($number) - 1));
    { local $^W; $number += 0; }
    $number = int ($number);
    return $number * 10**3 if $lastchar eq 'k';
    return $number * 10**6 if $lastchar eq 'm';
    return $number * 10**9 if $lastchar eq 'g';
    return $number;
}


# Get list of all files in a directory ----------------------------------------

sub get_directory_entries ($) {
 my @list;
 local *DH;

    opendir DH, $_[0] || return [];
    @list = grep {!/^\.+$/} readdir DH;
    closedir DH;
#    foreach (@list) {
#        print ("Got dir entry: ".$_[0].'/'.$_."\n"); }
    return @list;
}


# Get filesize ----------------------------------------------------------------

sub get_file_size ($) {
 my @s;

    @s = stat ($_[0]);
    return @s ? $s[7] : -1;
}


# Make sure the sublevel directory exists -------------------------------------

sub ensure_target_directory ($$) {
 my ($directory, $filename) = @_;
 my @filename_split;
 my $idx;
 my $seglen;

    $seglen = SPLIT_FILENAME_SEGLEN;
    $filename =~ s/\///g;
#    logger ("Ensure_target_directory. Directory: $directory, filename: $filename");
    mkdir $directory unless -e $directory;
    $idx = SPLIT_FILENAME_DEPTH;
    if ($idx > 1) {
        @filename_split = split (/(.{$seglen})/, $filename, SPLIT_FILENAME_DEPTH);
#        print ("\nFilename segments: @filename_split. Length: ".$#filename_split."\n");
        $#filename_split -= 1;
        foreach (@filename_split) {
            next unless $_;
            $directory .= '/'.$_;
#            print ("Ensuring directory: $directory\n");
            mkdir $directory unless -e $directory; } }
}


# Cleanup directory -----------------------------------------------------------

sub cleanup_directory ($$) {
 my ($const, $path) = @_;

#    logger ("Cleanup_Directory: $const$path");
    while ($path) {
        if (-e $const.$path) {
#            print ("Unlinking $const$path\n");
            unlink $const.$path;
            rmdir $const.$path; }
        $path = substr ($path, 0, rindex ($path, '/')); }
}


# Check whether file type seems to be valid -----------------------------------

sub check_file_type ($$) {
 my ($kind, $filename) = @_;
 my $fileext;
 my $filesize;

    $filesize = get_file_size ($filename);
#    print ("Filesize of $filename: $filesize, kind: $kind\n");
    if ($min_filesize_incoming{$kind} > $filesize) {
        logger ("Fehler: Dateigröße zu gering: $filename ($filesize bytes)");
        return 0; }
    $fileext = lc (get_file_ext ($filename));
    if (!$fileext) {
        if (!ALLOW_EXTENSIONLESS) {
            logger ("Fehler: ungültiger Dateityp: keine Dateinamenserweiterung vorhanden in $filename ($filesize bytes)");
            return 0; }
        return 1; }
    if (USE_POSITIVES && !is_in_array ($list_ext_pos{$kind}, $fileext)) {
        logger ("Fehler: ungültiger Dateityp, Ausschluß durch Positiv-Liste: ".uc ($fileext)." in $filename ($filesize bytes)");
        return 0; }
    if (USE_NEGATIVES && is_in_array ($list_ext_neg{$kind}, $fileext)) {
        logger ("Fehler: ungültiger Dateityp, Ausschluß durch Negativ-Liste: ".uc ($fileext)." in $filename ($filesize bytes)");
        return 0; }
#    logger ("Datei akzeptiert: $filename (".int ($filesize / 1024)." KiB)");
    return 1;
}


# Determine whether a file appears to be video, image, sound or crap ----------
# This is a simple check by filename extension only as yet

sub get_file_type_by_ext ($) {
 my ($filename) = @_;
 my $fileext;
    
    $fileext = lc (get_file_ext ($filename));
    if ($fileext) {
        return 'photo' if is_in_array ($list_ext_pos{'photo'}, $fileext);
        return 'audio' if is_in_array ($list_ext_pos{'audio'}, $fileext);
        return 'video' if is_in_array ($list_ext_pos{'video'}, $fileext); }
    return '';
}


# Move source file to either processed, unrecognized or crap directories ------

sub move_source_file ($$$$) {
 my ($kind, $srcdir, $srcname, $target) = @_;
 my $srcname2;
 my $destname;

    die "move_source_file: undefined target $target" if !defined ($path{$target});
    $kind = 'video' if !$kind;
    # ensure_target_directory ($path{'base'}.$path{$kind}.$path{$target}[$variant], $srcname);
    $destname   = $path{'base'}.$path{$kind}.$path{$target}[$variant].$srcname;
    $srcname2   = $srcdir.$srcname;
#    logger ("Srcname: $srcname2, TargetName: $destname\n");
#    printf ("%8d bytes: %s\n", get_file_size ($srcname2), uc ($target));
    if ($target ne 'processed' && INFOFILE_ON_ERROR) {
        create_info_file ('video', $srcdir, $path{'base'}.$path{$kind}.$path{'result'}[$variant], $srcname, ''); }
    rename $srcname2, $destname;
    cleanup_directory ($srcdir, $srcname);
}


# Create info file, describing the created target file ------------------------

sub create_info_file ($$$$$) {
 my ($kind, $srcdir, $destdir, $filename, $fileinfo) = @_;
 my $destfilename;
 my $infofilename;
 my @statinfo1;
 my @statinfo2;
 local *FH;

    $infofilename = $path{'cache'}.$path{'infofile'}.get_filename_wo_ext ($filename).EXT_INFOFILE;
    $destfilename = $path{'base'}.$path{$kind}.$path{'result'}[$variant].get_filename_wo_ext ($filename).".flv";
#    logger ("Creating info file: $infofilename for $filename, srcdir: $srcdir, destdir: $destdir");
#    print ("\ncreate_info_file. srcdir: $srcdir, destdir: $destdir, filename: $filename, Infofilename: $infofilename\n");
    @statinfo1 = stat ($srcdir.$filename);
    @statinfo2 = stat ($destfilename);
    ensure_target_directory ($destdir, $filename);
    open (FH, ">", $infofilename) || return;
    if ($fileinfo) {            # Successfully processed file?
        return if !@statinfo1 || !@statinfo2;
        print FH
            "Ausgangsdatei:\n".
            "Upload-Zeitpunkt:  ".get_time_string ($statinfo1[9])."\n".
            "Pfad:              ".$filename."\n".
            "Dateiendung:       ".uc (get_file_ext ($filename))."\n".
            "Urspr. Dateigröße: ".$statinfo1[7]."\n".
            "Video-Bitrate:     ".$$fileinfo{'srcvideobitrate'}." kbps\n".
            "Audio-Bitrate:     ".$$fileinfo{'srcaudiobitrate'}." kbps\n".
            "Video-Codec:       ".$$fileinfo{'srcvideocodec'}."\n".
            "Video-Auflösung:   ".$$fileinfo{'srcwidth'}."x".$$fileinfo{'srcheight'}." pixels\n".
            "Audio-Codec:       ".$$fileinfo{'srcaudiocodec'}."\n".
            "Audio-Samplerate:  ".$$fileinfo{'srcaudiofreq'}." Hz\n".
            "Audio-Kanäle:      ".$$fileinfo{'srcaudiochannels'}."\n".
            "Länge:             ".$$fileinfo{'srcduration'}."\n\n".
            "Zieldatei:\n".
            "Erstellungszeit:   ".get_time_string (0)."\n".
            "Pfad:              ".$ARGV[11]."\n".
            "Dateiendung:       FLV\n".
            "Videocodec:        Flash Video\n".
            "Video-Auflösung:   ".$parm_video{'width'}[$variant]."x".$parm_video{'height'}[$variant]." pixels\n".
            "Audio-Codec:       mp3\n".
            "Audio-Bitrate:     ".$parm_video{'audiorate'}[$variant]." kbps\n".
            "Audio-Samplerate:  ".$parm_video{'audiofreq'}[$variant]." Hz\n".
            "Einzelbilder:      ".$$fileinfo{'destframes'}."\n".
            "Gesamt-Bitrate:    ".$$fileinfo{'destbitrate'}."\n".
            "Dateigröße:        ".$statinfo2[7]."\n"; }
    else {                      # Crap or unrecognized file
        @statinfo1 = (0, 0, 0, 0, 0, 0, 0, 0, 0, 0) if !@statinfo1;
        print FH
            "FAILURE!\n".
            "Ausgangsdatei:\n".
            "Upload-Zeitpunkt:  ".get_time_string ($statinfo1[9])."\n".
            "Pfad:              ".$filename."\n".
            "Dateiendung:       ".uc (get_file_ext ($filename))."\n".
            "Urspr. Dateigröße: ".$statinfo1[7]."\n"};
    close FH;
}


# Get list of all (sub)directory entries of given directory list --------------

sub get_subdir_list ($$) {
 my ($directory, $list) = @_;
 my $subdir;
 my @targetlist = ();
 my @direntries;
 my $idx;

    return get_directory_entries ($directory) if $#$list == -1;
    for $subdir (@$list) {
        @direntries = get_directory_entries ($directory.'/'.$subdir);
        for ($idx = $#direntries; $idx >= 0; $idx--) {
            $direntries[$idx] = $subdir.'/'.$direntries[$idx]; }
#            print "Got dir: ".$direntries[$idx]."\n"; }
        push (@targetlist, @direntries); }
    return @targetlist;
}


# Get list of all files in path, regarding subdirectory structure -------------

sub get_all_files_in_path ($) {
 my ($directory) = @_;
 my @subdirs = ();
 my @files;
 my @subfiles;
 my $depth_counter;
 my $subdir;

    $depth_counter = SPLIT_FILENAME_DEPTH;
    if ($depth_counter) {
        while (--$depth_counter > 0) {
            @subdirs = get_subdir_list ($directory, \@subdirs); }
        @files = ();
        foreach $subdir (@subdirs) {
#            print ("Scanning subdir: $subdir\n");
            @subfiles = get_directory_entries ($directory.'/'.$subdir);
            foreach (@subfiles) {
                $_ = $subdir.'/'.$_; }
            push (@files, @subfiles); } }
    else {
        @files = get_directory_entries ($directory); }
    logger ("No new files to process.") if !@files;
    return @files;
}


# Process all files in the incoming directory ---------------------------------

sub process_all_files_in_path ($) {
 my ($directory) = @_;
 my @files;
 my $filename;
 my $filetype;
 my $targetloc;
 
    @files = get_all_files_in_path ($directory);
    if ($#files >= 0) {
        for $filename (@files) {
            $filetype = get_file_type_by_ext ($directory.$filename);
#            printf ("Processing %-50.50s  filetype: %s...", $directory.$filename."", $filetype ? $filetype : 'unrecogized');
            $targetloc = 'crap';
            if ($filetype && check_file_type ($filetype, $directory.$filename)) {
                if ($filetype eq 'video') {
                    $targetloc = process_video ($directory, $filename); }
                elsif ($filetype eq 'photo') {
                    $targetloc = process_photo ($directory, $filename); }
                elsif ($filetype eq 'audio') {
                    $targetloc = process_photo ($directory, $filename); } }
            move_source_file ($filetype, $directory, $filename, $targetloc); } }
    return $#files + 1;
}


# Process all video files in video incoming directory -------------------------

sub process_all_videos () {
 my ($directory) = @_;
 my @files;
 my $filename;

    @files = get_all_files_in_path ($directory);
    if ($#files >= 0) {
        for $filename (@files) {
            printf ("Processing %-50.50s ", $directory.$filename."...");
            move_source_file ('video', $directory, $filename, check_file_type ('video', $directory.$filename) ? process_video ($directory, $filename) : 'crap'); } }
    return $#files + 1;
}


# Process single video file ---------------------------------------------------

sub process_video ($$) {
 my ($directory, $filename) = @_;
 my $cmd;
 my $basename;
 my $quality;
 my $targetname;
 my $rc;
 my $result;
 my @output;
 local *FH;

    $quality = $parm_video{'quality'}[$variant] ? ' -qscale '.$parm_video{'quality'}[$variant] : ' -b '.$parm_video{'bitrate'}[$variant].'k';
    $basename = get_filename_wo_ext ($filename);
    $targetname = $path{'base'}.$path{'video'}.$path{'result'}[$variant].$basename.".flv";
    ensure_target_directory ($path{'base'}.$path{'video'}.$path{'result'}[$variant], $filename);
    print ("Processing video file: $directory$filename, basename: $basename, target: $targetname\n");
    $cmd = "nice -n ".NICELEVEL." time ".$tool{'ffmpeg'}." 2>&1 -y -i $directory$filename ".
#			"-acodec mp3".		# Enable if ffmpeg has mp3 support
        ($parm_video{'maxfilesize'}[$variant] ? " -fs ".get_number ($parm_video{'maxfilesize'}[$variant]) : "").
        ($parm_video{'maxduration'}[$variant] ? " -t ".$parm_video{'maxduration'}[$variant] : "").
        " -ab ".$parm_video{'audiorate'}[$variant].
        " -ar ".$parm_video{'audiofreq'}[$variant].
        " -r ".$parm_video{'framerate'}[$variant].
        " -s ".$parm_video{'width'}[$variant]."x".$parm_video{'height'}[$variant].
        " ".$parm_video{'custom'}[$variant].
        $quality." ".
        $targetname." |";
    print "Command: $cmd\n";
    open FH, $cmd  || die ("Can't open tool ".$tool{'ffmpeg'});
    while (<FH>) {
#        print "FFMPEG output: $_";
        chomp;
        push @output, $_; }
    close FH;
    $rc = $?;
    $result = evaluate_video ($directory, $filename, $rc, \@output);
    if ($result eq 'processed') {
        create_screenshot ($directory, $filename);
        create_thumbnail ($directory, $filename);
	}
    else {
        cleanup_directory ($path{'base'}.$path{'video'}.$path{'result'}[$variant], $basename.'.flv'); }
    return $result;
}


# Create screenshot from video file -------------------------------------------

sub create_screenshot ($$) {
 my ($directory, $filename) = @_;
 my $cmd;
 my $targetname;

    $targetname = $path{'base'}.$path{'video'}.$path{'sshot'}[$variant].get_filename_wo_ext ($filename).EXT_SCREENSHOT;
    $cmd = "nice -n ".NICELEVEL." ".$tool{'ffmpeg'}.
        " >/dev/zero 2>&1 -y -i $directory$filename -ss ".$parm_sshot{'time'}[$variant].
        " -s ".$parm_sshot{'width'}[$variant]."x".$parm_sshot{'height'}[$variant].
        " -vframes 1 -f image2".
        " -qcomp ".$parm_sshot{'qual'}[$variant]." $targetname";
    print "\n\n\nCommand: $cmd\n\n\n";
    system ($cmd);
}

# Create thumbnail from video file -------------------------------------------

sub create_thumbnail ($$) {
 my ($directory, $filename) = @_;
 my $cmd;
 my $targetname;

    $targetname = $path{'base'}.$path{'video'}.$path{'sshot'}[$variant]."thumb_".get_filename_wo_ext ($filename).EXT_SCREENSHOT;
    $cmd = "nice -n ".NICELEVEL." ".$tool{'ffmpeg'}.
        " >/dev/zero 2>&1 -y -i $directory$filename -ss ".$parm_sshot{'time'}[$variant].
        " -s ".$parm_thumbnail{'width'}[$variant]."x".$parm_thumbnail{'height'}[$variant].
        " -vframes 1 -f image2".
        " -qcomp ".$parm_sshot{'qual'}[$variant]." $targetname";
    print "\n\n\nthumb Command: $cmd\n\n\n";
    system ($cmd);
}



# Get FFMPEG error message ----------------------------------------------------

sub get_ffmpeg_error_text ($) {
 my ($lines) = @_;

    return (substr ($$lines[@$lines - 2], 0, 1) eq " " ? "" : $$lines[@$lines - 2].", ").$$lines[@$lines -1];
}


# Get file magic --------------------------------------------------------------

sub get_file_magic ($) {
 my ($filename) = @_;
 local *FH;

    open FH, $tool{'magic'}." $filename |";
    $_ = <FH>;
    chomp;
    close FH;
    return $_;
}


# Evaluate result of video conversion by FFMPEG output ------------------------

sub evaluate_video ($$$$) {
 my ($directory, $filename, $rc, $output) = @_;
 my $targetname;
 my $filesize;
 my $line;
 my %fileinfo;

    $filesize = get_file_size ($filename);
    %fileinfo = analyze_ffmpeg_output ($output);
    $targetname = $path{'base'}.$path{'video'}.$path{'result'}[$variant].get_filename_wo_ext ($filename).".flv";
    if ($rc) {
        unlink $targetname;
        logger ("Fehler: $filename ($filesize bytes): FFMPEG Fehlercode $rc, Fehlertext: \"".get_ffmpeg_error_text ($output)."\"");
        if ($fileinfo{'srcwidth'} ne '-') {
            logger ("Fehler: $filename, ".$fileinfo{'srcwidth'}."x".$fileinfo{'srcheight'}.", ".$fileinfo{'srcvideobitrate'}." / ".$fileinfo{'srcaudiobitrate'}." kbps, videocodec: ".$fileinfo{'srcvideocodec'}.", audiocodec: ".$fileinfo{'srcaudiocodec'});
            return 'unrecognized'; }
        else {
            logger ("Fehler: ".get_file_magic ($filename));
            return 'crap'; }
        return 0; }
    $filesize = get_file_size ($targetname);
    if ($filesize < $min_filesize_processed{'video'}) {
        unlink $targetname;
        logger ("Fehler: $targetname ($filesize bytes) zu klein; kein gültiges Video");
        return 'crap'; }
    logger ("Konvertiert: $filename, ".$fileinfo{'srcwidth'}."x".$fileinfo{'srcheight'}.", ".$fileinfo{'srcvideobitrate'}." / ".$fileinfo{'srcaudiobitrate'}." kbps, videocodec: ".$fileinfo{'srcvideocodec'}.", audiocodec: ".$fileinfo{'srcaudiocodec'});
    create_info_file ('video', $path{'base'}.$path{'video'}.$path{'incoming'}, $path{'base'}.$path{'video'}.$path{'result'}[$variant], $filename, \%fileinfo);
#    foreach $line (@$output) {
#        print $line."\n"; }
    return 'processed';
}


# Check whether the given string represents a resolution as output by FFMPEG --

sub is_resolution ($) {
 my ($text) = @_;
 my $idx;
 my $char;

    $idx = index ($text, "x");
    return 0 if $idx <= 1 || $idx >= length ($text);
    $char = substr ($text, $idx - 1, 1);
    return 0 if $char < '0' || $char > '9';
    $char = substr ($text, $idx + 1, 1);
    return $char < '0' || $char > '9' ? 0 : 1;
}


# Evaluate FFMPEG output and extract valuable information from it -------------

sub analyze_ffmpeg_output ($) {
 my ($output) = @_;
 my $idx;
 my $idx2;
 my @tokens;
 my $state;
 my %info = (
    'srcduration'       => '-',
    'srcvideobitrate'   => '-',
    'srcaudiobitrate'   => '-',
    'srcaudiocodec'     => '-',
    'srcaudiofreq'      => '-',
    'srcvideobitrate'   => '-',
    'srcvideocodec'     => '-',
    'srcaudiochannels'  => '-',
    'srcwidth'          => '-',
    'srcheight'         => '-',
    'destframes'        => '-',
    'destbitrate'       => '-');

    for ($idx = 0; $idx < @$output; $idx++) {
        last if substr (@$output[$idx], 0, 7) eq "Input #"; }
    $idx++;
    while ($idx < @$output && substr (@$output[$idx], 0, 8) ne "Output #" && $idx < @$output) {
        push (@tokens, split (/[, ]+/, @$output[$idx++])); }
    $idx = @$output - 2;
    while ($idx < @$output) {
#        print ("Got line: ".@$output[$idx]."\n");
        push (@tokens, split (/[ ]+/, @$output[$idx++])); }
    for ($idx = 0; $idx < $#tokens; $idx++) {
        $info{'srcduration'}        = $tokens[++$idx]   if $tokens[$idx] eq "Duration:";
        $info{'srcvideobitrate'}    = $tokens[++$idx]   if $tokens[$idx] eq "bitrate:";
        $info{'srcaudiocodec'}      = $tokens[++$idx]   if $tokens[$idx] eq "Audio:";
        $info{'srcaudiofreq'}       = $tokens[$idx - 1] if $tokens[$idx] eq "Hz";
        $info{'srcaudiochannels'}   = $tokens[$idx + 1] if $tokens[$idx] eq "Hz";
        $info{'srcaudiobitrate'}    = $tokens[$idx + 2] if $tokens[$idx] eq "Hz";
        $info{'destframes'}         = $tokens[$idx - 1] if $tokens[$idx] eq "fps=";
        $info{'destbitrate'}        = $tokens[$idx + 1] if $tokens[$idx] eq "bitrate=";
        if ($tokens[$idx] eq "Video:" && $info{'srcvideocodec'} eq '-') {
            $info{'srcvideocodec'} = $tokens[++$idx];
            $idx++;
            while ($idx < $#tokens && !is_resolution ($tokens[$idx])) {
                $info{'srcvideocodec'} .= ", ".$tokens[$idx] if length ($tokens[$idx]) > 1;
                $idx++; }
            if ($idx < $#tokens && is_resolution ($tokens[$idx])) {
                ($info{'srcwidth'}, $info{'srcheight'}) = split (/x/, $tokens[$idx++]);
                $info{'srcfps'} = $tokens[$idx++]; } } }
    
# For debugging purposes:
#    foreach (@tokens) {
#        print ("Found token: \"$_\"\n"); }
#    foreach (%info) {
#        print ("Got info: $_\n"); }

    return %info;
}


# Process single photo file ---------------------------------------------------

sub process_photo ($$) {
 my ($directory, $filename) = @_;
 my $cmd;
 my $basename;
 my $quality;
 my $targetname;
 my $rc;
 my $result;
 my @output;
 local *FH;

    $quality = $parm_video{'quality'}[$variant] ? " -qscale ".$parm_video{'quality'}[$variant] : " -b ".$parm_video{'bitrate'}[$variant];
    $basename = get_filename_wo_ext ($filename);
    $targetname = $path{'base'}.$path{'photo'}.$path{'result'}[$variant].$basename.".jpg";
    ensure_target_directory ($path{'base'}.$path{'photo'}.$path{'result'}[$variant], $filename);
#    print ("Processing photo file: $directory$filename, basename: $basename, target: $targetname\n");
    $cmd = "nice -n ".NICELEVEL." ".$tool{'imgconvert'}.' 2>&1 -size '.$parm_photo{'width'}[$variant]."x".$parm_photo{'height'}[$variant]." ".$directory.$filename;
    if (CREATE_THUMBNAILS) {
        $cmd .= ' -thumbnail '.$parm_thumb{'width'}[$variant].'x'.$parm_thumb{'height'}[$variant]; }
    $cmd .= ' '.$parm_photo{'extra'}[$variant].' '.$targetname;
#    print "Command: $cmd\n";
    system $cmd || die ("Can't open tool ".$tool{'imgconvert'});
#    open FH, $cmd;
#    while (<FH>) {
#        print "ImgConvert output: $_";
#        chomp;
#        push @output, $_; }
#    close FH;
    $rc = $?;
#    print ("Image convert result: $?\n");
    $result = ($? ? 'unrecognized' : 'processed');
    if ($result ne 'processed') {
        cleanup_directory ($path{'base'}.$path{'photo'}.$path{'result'}[$variant], $basename.'.jpg'); }
#    create_info_file ('photo', $path{'base'}.$path{'photo'}.$path{'incoming'}, $path{'base'}.$path{'photo'}.$path{'result'}[$variant], $filename, \%fileinfo);
    return $result;
}


# Process single sound file ---------------------------------------------------

sub process_audio ($$) {
    return 'unrecognized';
}



# # Convert income sound files to MP3
# 
# function convert_sounds () {
# SUBDIR=$PATH_BASE/$PATH_MP3_I
# for SUBDIR in $PATH_BASE/$PATH_MP3_I/*; do
#     [ "${SUBDIR##*/}" = \* ] && break;
#     [ -d $PATH_BASE/$PATH_MP3_T/${SUBDIR##*/} ] || mkdir $PATH_BASE/$PATH_MP3_T/${SUBDIR##*/}
#     [ -d $PATH_BASE/$PATH_MP3_P/${SUBDIR##*/} ] || mkdir $PATH_BASE/$PATH_MP3_P/${SUBDIR##*/}
#     for FILE in $SUBDIR/*; do
# #        echo Found file: $FILE
#         [ "${FILE##*/}" = \* ] && break;
#         SOURCEFILE="$FILE"
#         FILE=${FILE##*/}
#         FILEEXT=${FILE##*.}
#         [ "$FILEEXT" = "mp3" -o "$FILEEXT" = "MP3" ] && FILETYPE="-t mp3" || FILETYPE=""
#         TARGETFILE="$PATH_BASE/$PATH_MP3_T/${SUBDIR##*/}/${FILE%.*}.mp3"
#         STARTTIME=$(date +%s%N)
#         nice -n $NICELEVEL $SOX 2>/dev/zero $FILETYPE "$SOURCEFILE" -t wav - fade q 0 $AUDIO_DURATION $[AUDIO_DURATION - FADEOUT] | nice -n $NICELEVEL $LAME 2>/dev/zero -b $AUDIO_BITRATE -m j -q $AUDIO_QUALITY --resample $AUDIO_SAMPLERATE - "$TARGETFILE"
#         ERRCODE=$?
#         ENDTIME=$(date +%s%N)
#         if [ $ERRCODE -gt 0 ]; then
#             echo -e "Error converting sound file $FILE"
#             [ -f "$TARGETFILE" ] && rm "$TARGETFILE"
#             [ -d $PATH_BASE/$PATH_MP3_E/${SUBDIR##*/} ] || mkdir $PATH_BASE/$PATH_MP3_E/${SUBDIR##*/}
#             mv "$SOURCEFILE" "$PATH_BASE/$PATH_MP3_E/${SUBDIR##*/}"
#             continue
#         fi
#         FILESIZE1=$(ls -l "$SOURCEFILE" | awk '{ print $5;}')
#         FILESIZE1=$[FILESIZE1/1024]
# #        rm -v $PATH_BASE/$PATH_MOVIES_I/$FILE
#         FILESIZE2=$(ls -l "$TARGETFILE" | awk '{ print $5;}')
#         FILESIZE2=$[FILESIZE2/1024]
#         echo -e "Successfully converted sound file $FILE, from $FILESIZE1 to $FILESIZE2 KBytes in $[(ENDTIME-STARTTIME)/1000000] milliseconds."
#         mv "$SOURCEFILE" "$PATH_BASE/$PATH_MP3_P/${SUBDIR##*/}"
#     done
# #    rmdir $SUBDIR
# done
# }
# 
# 
# # Convert income image files to JPG
# 
# function convert_pictures () {
# SUBDIR=$PATH_BASE/$PATH_PICS_I
# for SUBDIR in $PATH_BASE/$PATH_PICS_I/*; do
#     [ "${SUBDIR##*/}" = \* ] && break;
#     [ -d $PATH_BASE/$PATH_PICS_T/${SUBDIR##*/} ] || mkdir $PATH_BASE/$PATH_PICS_T/${SUBDIR##*/}
#     [ -d $PATH_BASE/$PATH_PICS_P/${SUBDIR##*/} ] || mkdir $PATH_BASE/$PATH_PICS_P/${SUBDIR##*/}
#     for FILE in $SUBDIR/*; do
# #        echo Found file: $FILE
#         [ "${FILE##*/}" = \* ] && break;
#         SOURCEFILE=$FILE
#         FILE=${FILE##*/}
#         TARGETFILE="$PATH_BASE/$PATH_PICS_T/${SUBDIR##*/}/${FILE%.*}.jpg"
#         STARTTIME=$(date +%s%N)
# #        nice -n $NICELEVEL $IMGCONVERT 2>/dev/zero -geometry ${SSHOT_WIDTH}x${SSHOT_HEIGHT} "$SOURCEFILE" "$TARGETFILE"
# #        nice -n $NICELEVEL $IMGCONVERT 2>/dev/zero -thumbnail "${SSHOT_WIDTH}x${SSHOT_HEIGHT}>" "$SOURCEFILE" "$TARGETFILE"
#         nice -n $NICELEVEL $IMGCONVERT 2>/dev/zero -size "${SSHOT_WIDTH}x${SSHOT_HEIGHT}" "$SOURCEFILE" -thumbnail "${SSHOT_WIDTH}x${SSHOT_HEIGHT}>" -bordercolor "#FFFFFF" -border 60 -gravity center -crop ${SSHOT_WIDTH}x${SSHOT_HEIGHT}+0+0 +repage "$TARGETFILE"
#         ERRCODE=$?
#         ENDTIME=$(date +%s%N)
#         if [ $ERRCODE -gt 0 ]; then
#             echo -e "Error converting image file $FILE"
#             [ -f "$TARGETFILE" ] && rm "$TARGETFILE"
#             [ -d $PATH_BASE/$PATH_PICS_E/${SUBDIR##*/} ] || mkdir $PATH_BASE/$PATH_PICS_E/${SUBDIR##*/}
#             mv "$SOURCEFILE" "$PATH_BASE/$PATH_PICS_E/${SUBDIR##*/}"
#             continue
#         fi
#         FILESIZE1=$(ls -l "$SOURCEFILE" | awk '{ print $5;}')
#         FILESIZE1=$[FILESIZE1/1024]
# #        rm -v $PATH_BASE/$PATH_MOVIES_I/$FILE
#         FILESIZE2=$(ls -l "$TARGETFILE" | awk '{ print $5;}')
#         FILESIZE2=$[FILESIZE2/1024]
#         echo -e "Successfully converted image file $FILE, from $FILESIZE1 to $FILESIZE2 KBytes in $[(ENDTIME-STARTTIME)/1000000] milliseconds."
#         mv "$SOURCEFILE" "$PATH_BASE/$PATH_PICS_P/${SUBDIR##*/}"
#     done
# #    rmdir $SUBDIR
# done
# }
# 