import React, { useState } from 'react';
import { Mail, MapPin, Facebook, Instagram, Linkedin } from 'lucide-react';

const ContactSection: React.FC = () => {
  const [formData, setFormData] = useState({ name: '', email: '', subject: '', message: '' });
  const [successMessage, setSuccessMessage] = useState('');

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>) => {
    const { name, value } = e.target;
    setFormData({ ...formData, [name]: value });
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    console.log(formData);
    setSuccessMessage('Thank you for reaching out! We will get back to you soon.');
    setFormData({ name: '', email: '', subject: '', message: '' });
  };

  return (
    <section className="py-16 bg-gradient-to-b from-slate-100 via-slate-50/80 to-slate-100 dark:from-slate-950 dark:via-slate-900/80 dark:to-slate-950 text-gray-900 dark:text-white transition-colors duration-300">
      <div className="container mx-auto px-4">
        <h2 className="text-3xl font-bold text-center mb-8 text-gray-900 dark:text-white">Get In Touch</h2>
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <form onSubmit={handleSubmit} className="bg-white/80 dark:bg-slate-800/60 backdrop-blur-sm p-6 rounded-lg border border-purple-200 dark:border-purple-500/20 shadow-lg dark:shadow-md">
            <div className="mb-4">
              <label htmlFor="name" className="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-200">Name</label>
              <input
                type="text"
                id="name"
                name="name"
                value={formData.name}
                onChange={handleChange}
                required
                className="w-full p-3 rounded-lg bg-gray-100 dark:bg-slate-900/80 text-gray-900 dark:text-white border border-gray-300 dark:border-slate-600 focus:outline-none focus:ring-2 focus:ring-purple-500"
              />
            </div>
            <div className="mb-4">
              <label htmlFor="email" className="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-200">Email</label>
              <input
                type="email"
                id="email"
                name="email"
                value={formData.email}
                onChange={handleChange}
                required
                className="w-full p-3 rounded-lg bg-gray-100 dark:bg-slate-900/80 text-gray-900 dark:text-white border border-gray-300 dark:border-slate-600 focus:outline-none focus:ring-2 focus:ring-purple-500"
              />
            </div>
            <div className="mb-4">
              <label htmlFor="subject" className="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-200">Subject</label>
              <select
                id="subject"
                name="subject"
                value={formData.subject}
                onChange={handleChange}
                required
                className="w-full p-3 rounded-lg bg-gray-100 dark:bg-slate-900/80 text-gray-900 dark:text-white border border-gray-300 dark:border-slate-600 focus:outline-none focus:ring-2 focus:ring-purple-500"
              >
                <option value="">Select a subject</option>
                <option value="General Inquiry">General Inquiry</option>
                <option value="Support">Support</option>
                <option value="Feedback">Feedback</option>
              </select>
            </div>
            <div className="mb-4">
              <label htmlFor="message" className="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-200">Message</label>
              <textarea
                id="message"
                name="message"
                value={formData.message}
                onChange={handleChange}
                required
                rows={4}
                className="w-full p-3 rounded-lg bg-gray-100 dark:bg-slate-900/80 text-gray-900 dark:text-white border border-gray-300 dark:border-slate-600 focus:outline-none focus:ring-2 focus:ring-purple-500"
              ></textarea>
            </div>
            <button
              type="submit"
              className="w-full bg-purple-600 text-white py-3 rounded-lg hover:bg-purple-700 transition"
            >
              Send
            </button>
            {successMessage && <p className="mt-4 text-green-400">{successMessage}</p>}
          </form>

          <div className="space-y-6">
            <div className="bg-white/80 dark:bg-slate-800/60 backdrop-blur-sm p-6 rounded-lg border border-purple-200 dark:border-purple-500/20 shadow-lg dark:shadow-md">
              <Mail className="text-purple-500 dark:text-purple-400 mb-2" />
              <h3 className="text-xl font-semibold mb-2 text-gray-900 dark:text-white">Email</h3>
              <p className="text-gray-600 dark:text-gray-300">uommediaunit@gmail.com</p>
            </div>
            <div className="bg-white/80 dark:bg-slate-800/60 backdrop-blur-sm p-6 rounded-lg border border-purple-200 dark:border-purple-500/20 shadow-lg dark:shadow-md">
              <MapPin className="text-purple-500 dark:text-purple-400 mb-2" />
              <h3 className="text-xl font-semibold mb-2 text-gray-900 dark:text-white">Location</h3>
              <p className="text-gray-600 dark:text-gray-300">University of Moratuwa, Katubedda, Moratuwa, Sri Lanka</p>
            </div>
            <div className="flex space-x-4">
              <a href="#" className="text-purple-400 hover:text-purple-300">
                <Facebook size={24} />
              </a>
              <a href="#" className="text-purple-400 hover:text-purple-300">
                <Instagram size={24} />
              </a>
              <a href="#" className="text-purple-400 hover:text-purple-300">
                <Linkedin size={24} />
              </a>
            </div>
            <div className="rounded-lg overflow-hidden">
              <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3168.948090123456!2d79.9028!3d6.7956!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae25b0f0f0f0f0f%3A0x0!2sUniversity%20of%20Moratuwa!5e0!3m2!1sen!2slk!4v1234567890"
                width="100%"
                height="250"
                style={{ border: 0 }}
                allowFullScreen
                loading="lazy"
                referrerPolicy="no-referrer-when-downgrade"
              ></iframe>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};

export default ContactSection;